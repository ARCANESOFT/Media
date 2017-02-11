<?php namespace Arcanesoft\Media;

use Arcanesoft\Media\Contracts\Media as MediaContract;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\UploadedFile;

/**
 * Class     Media
 *
 * @package  Arcanesoft\Media
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Media implements MediaContract
{
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
        return $this->filesystem()->disk($driver);
    }

    /**
     * Get the default filesystem adapter.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter|\Illuminate\Contracts\Filesystem\Filesystem
     */
    public function defaultDisk()
    {
        return $this->disk($this->getDefaultDiskName());
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
        $directories = array_map(function ($dir) use ($directory) {
            return [
                'name' => str_replace("$directory/", '', $dir),
                'path' => $dir,
            ];
        }, $this->defaultDisk()->directories($directory));

        return Entities\DirectoryCollection::make($directories);
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
        $disk  = $this->defaultDisk();

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

        return Entities\FileCollection::make($files);
    }

    /**
     * Get all the directories & files from a given location.
     *
     * @param  string  $directory
     *
     * @return array
     */
    public function all($directory)
    {
        $directories = $this->directories($directory)->transform(function ($item) {
            return $item + ['type' => self::MEDIA_TYPE_DIRECTORY];
        })->toArray();

        $files = $this->files($directory)->transform(function (array $item) {
            return $item + ['type' => self::MEDIA_TYPE_FILE];
        })->toArray();

        return array_merge($directories, $files);
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
        return $this->defaultDisk()->makeDirectory($path);
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
        return $this->defaultDisk()->move($from, $to);
    }
}
