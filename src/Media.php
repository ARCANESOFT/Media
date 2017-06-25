<?php namespace Arcanesoft\Media;

use Arcanesoft\Media\Contracts\Media as MediaContract;
use Arcanesoft\Media\Entities\DirectoryCollection;
use Arcanesoft\Media\Entities\FileCollection;
use Arcanesoft\Media\Exceptions\DirectoryNotFound;
use Arcanesoft\Media\Exceptions\FileNotFoundException;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Class     Media
 *
 * @package  Arcanesoft\Media
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Media implements MediaContract
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    const VERSION = '2.2.2';

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Media constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the Filesystem Manager instance.
     *
     * @return \Illuminate\Contracts\Filesystem\Factory
     */
    public function filesystem()
    {
        return $this->app->make('filesystem');
    }

    /**
     * Get the Config Repository.
     *
     * @return \Illuminate\Contracts\Config\Repository
     */
    protected function config()
    {
        return $this->app->make('config');
    }

    /**
     * Get the default disk name.
     *
     * @return string
     */
    public function getDefaultDiskName()
    {
        return $this->config()->get('arcanesoft.media.filesystem.default');
    }

    /**
     * Get excluded directories.
     *
     * @return array
     */
    public function getExcludedDirectories()
    {
        return Helpers\ExcludePattern::directories(
            $this->config()->get('arcanesoft.media.directories.excluded', [])
        );
    }

    /**
     * Get excluded files.
     *
     * @return array
     */
    public function getExcludedFiles()
    {
        return $this->config()->get('arcanesoft.media.files.excluded', []);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get a filesystem adapter.
     *
     * @param  string|null  $driver
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter|\Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($driver = null)
    {
        return $this->filesystem()->disk($driver ?: $this->getDefaultDiskName());
    }

    /**
     * Get all the directories & files from a given location.
     *
     * @param  string  $directory
     *
     * @return array
     */
    public function all($directory = '/')
    {
        $directories = $this->directories($directory)->transform(function ($item) {
            return $item + ['type' => self::MEDIA_TYPE_DIRECTORY];
        });

        $files = $this->files($directory)->transform(function (array $item) {
            return $item + ['type' => self::MEDIA_TYPE_FILE];
        });

        return array_merge($directories->toArray(), $files->toArray());
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string  $directory
     *
     * @return \Arcanesoft\Media\Entities\DirectoryCollection
     */
    public function directories($directory)
    {
        $this->checkDirectory($directory);

        $directories = array_map(function ($dir) use ($directory) {
            return [
                'name' => str_replace("$directory/", '', $dir),
                'path' => $dir,
            ];
        }, $this->disk()->directories($directory));

        return DirectoryCollection::make($directories)
            ->exclude($this->getExcludedDirectories());
    }

    /**
     * Get a collection of all files in a directory.
     *
     * @param  string  $directory
     *
     * @return \Arcanesoft\Media\Entities\FileCollection
     */
    public function files($directory)
    {
        $this->checkDirectory($directory);

        $disk = $this->disk();

        // TODO: Add a feature to exclude unwanted files.
        $files = array_map(function ($filePath) use ($disk, $directory) {
            return [
                'name'         => str_replace("$directory/", '', $filePath),
                'path'         => $filePath,
                'url'          => $disk->url($filePath),
                'mimetype'     => $disk->mimeType($filePath),
                'lastModified' => Carbon::createFromTimestamp($disk->lastModified($filePath))->toDateTimeString(),
                'visibility'   => $disk->getVisibility($filePath),
                'size'         => $disk->size($filePath),
            ];
        }, $disk->files($directory));

        return FileCollection::make($files)->exclude($this->getExcludedFiles());
    }

    /**
     * Get the file details.
     *
     * @param  string  $path
     *
     * @return array
     */
    public function file($path)
    {
        return $this->files(dirname($path))->first(function ($file) use ($path) {
            return $file['path'] === $path;
        }, function () use ($path) {
            throw new FileNotFoundException("File [$path] not found!");
        });
    }

    /**
     * Store an array of files.
     *
     * @param  string  $directory
     * @param  array   $files
     */
    public function storeMany($directory, array $files)
    {
        foreach ($files as $file) {
            $this->store($directory, $file);
        }
    }

    /**
     * Store a file.
     *
     * @param  string                         $directory
     * @param  \Illuminate\Http\UploadedFile  $file
     *
     * @return string|false
     */
    public function store($directory, UploadedFile $file)
    {
        return $file->store($directory, $this->getDefaultDiskName());
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function makeDirectory($path)
    {
        return $this->disk()->makeDirectory($path);
    }

    /**
     * Delete a directory.
     *
     * @param  string  $directory
     *
     * @return bool
     */
    public function deleteDirectory($directory)
    {
        return $this->disk()->deleteDirectory($directory);
    }

    /**
     * Delete a file.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function deleteFile($path)
    {
        return $this->disk()->delete($path);
    }

    /**
     * Move a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     *
     * @return bool
     */
    public function move($from, $to)
    {
        return $this->disk()->move($from, $to);
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Determine if a file/directory exists.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function exists($path)
    {
        return $this->disk()->exists($path);
    }

    /**
     * Check if the directory is excluded.
     *
     * @param  string  $directory
     *
     * @return bool
     */
    public function isExcludedDirectory($directory)
    {
        foreach ($this->getExcludedDirectories() as $pattern) {
            if (Str::is($pattern, $directory)) return true;
        }

        return false;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check the given directory location.
     *
     * @param  string  &$directory
     *
     * @throws \Arcanesoft\Media\Exceptions\DirectoryNotFound
     * @throws \Arcanesoft\Media\Exceptions\AccessNotAllowed
     */
    protected function checkDirectory(&$directory)
    {
        $directory = trim($directory, '/');

        $this->checkDirectoryExists($directory);
        $this->checkDirectoryAccess($directory);
    }

    /**
     * Check if the directory exists.
     *
     * @param  string  $directory
     *
     * @throws \Arcanesoft\Media\Exceptions\DirectoryNotFound
     */
    protected function checkDirectoryExists($directory)
    {
        if ( ! empty($directory) && ! $this->exists($directory)) {
            throw new DirectoryNotFound("Directory [$directory] not found !", 404);
        }
    }

    /**
     * Check if can access the directory.
     *
     * @param  string  $directory
     *
     * @throws \Arcanesoft\Media\Exceptions\AccessNotAllowed
     */
    protected function checkDirectoryAccess($directory)
    {
        if ($this->isExcludedDirectory($directory)) {
            throw new Exceptions\AccessNotAllowed('Access not allowed.', 405);
        }
    }
}
