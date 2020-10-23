<?php namespace Arcanesoft\Media;

use Arcanesoft\Media\Entities\DirectoryItem;
use Arcanesoft\Media\Entities\FileItem;
use Arcanesoft\Media\Entities\MediaCollection;
use Arcanesoft\Media\Exceptions\DirectoryNotFoundException;
use Arcanesoft\Media\Exceptions\FileNotFoundException;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use phpDocumentor\Reflection\File;

class MediaManager
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var \Illuminate\Contracts\Filesystem\Factory
     */
    private $filesystem;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * MediaManager constructor.
     *
     * @param  \Illuminate\Contracts\Filesystem\Factory  $filesystem
     */
    public function __construct(Factory $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Get the filesystem's storage disk.
     *
     * @param  string|null  $name
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\FilesystemAdapter
     */
    public function disk($name = null): Filesystem
    {
        return $this->filesystem->disk(
            $name ?: $this->getDefaultDisk()
        );
    }

    /**
     * Get the default disk.
     *
     * @return string
     */
    public function getDefaultDisk(): string
    {
        return 'media';
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  string|null  $directory
     * @param  bool         $recursive
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(string $directory = null, bool $recursive = false)
    {
        return $this->directories($directory, $recursive)
            ->merge(
                $this->files($directory, $recursive)
            );
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string|null  $directory
     * @param  bool         $recursive
     *
     * @return \Illuminate\Support\Collection
     */
    public function directories(string $directory = null, bool $recursive = false)
    {
        $disk        = $this->disk();
        $directories = $disk->directories($directory, $recursive);

        return MediaCollection::directories(array_map(function ($path) use ($disk) {
            return [
                'name' => basename($path),
            ];
        }, array_combine($directories, $directories)));
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param  string|null  $directory
     * @param  bool         $recursive
     *
     * @return \Illuminate\Support\Collection
     */
    public function files(string $directory = null, bool $recursive = false)
    {
        $disk  = $this->disk();
        $files = $disk->files($directory, $recursive);

        return MediaCollection::files(array_map(function ($path) use ($disk) {
            return [
                'name'         => basename($path),
                'path'         => $path,
                'url'          => $disk->url($path),
                'mimetype'     => $disk->mimeType($path),
                'lastModified' => Carbon::createFromTimestamp($disk->lastModified($path))->toDateTimeString(),
                'visibility'   => $disk->getVisibility($path),
                'size'         => $disk->size($path),
            ];
        }, array_combine($files, $files)));
    }

    /**
     * Get the file.
     *
     * @param  string  $path
     *
     * @return \Arcanesoft\Media\Entities\FileItem
     */
    public function file(string $path)
    {
        return $this->files(dirname($path))->first(function (FileItem $file) use ($path) {
            return $file->path === $path;
        }, function () use ($path) {
            throw new FileNotFoundException("File [$path] not found!");
        });
    }

    public function directory(string $path)
    {
        return $this->directories(dirname($path))->first(function (DirectoryItem $directory) use ($path) {
            return $directory->path === $path;
        }, function () use ($path) {
            throw new DirectoryNotFoundException("Directory [$path] not found!");
        });
    }

    /**
     * Check if the path exists.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function exists($path): bool
    {
        if ($path === '/')
            return true;

        return $this->disk()->exists($path);
    }

    /**
     * Store the uploaded file on the disk.
     *
     * @param  string                                               $path
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile  $file
     * @param  array                                                $options
     *
     * @return string|false
     */
    public function putFile(string $path, $file, array $options = [])
    {
        return $this->disk()->putFile($path, $file, $options);
    }

    /**
     * Make a directory.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function makeDirectory(string $path): bool
    {
        return $this->disk()->makeDirectory($path);
    }

    /**
     * Rename the media.
     *
     * @param  string  $oldName
     * @param  string  $newName
     *
     * @return bool
     */
    public function rename(string $oldName, string $newName): bool
    {
        return $this->disk()->rename($oldName, $newName);
    }

    /**
     * Move the media.
     *
     * @param  string  $from
     * @param  string  $to
     *
     * @return bool
     */
    public function move(string $from, string $to): bool
    {
        return $this->disk()->move($from, $to);
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     *
     * @return bool
     */
    public function deleteFile($paths): bool
    {
        return $this->disk()->delete($paths);
    }

    /**
     * Recursively delete a directory.
     *
     * @param  string  $directory
     *
     * @return bool
     */
    public function deleteDirectory(string $directory): bool
    {
        return $this->disk()->deleteDirectory($directory);
    }

    /**
     * Create a streamed download response for a given file.
     *
     * @param  string       $path
     * @param  string|null  $name
     * @param  array|null   $headers
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(string $path, string $name = null, array $headers = [])
    {
        return $this->disk()->download($path, $name, $headers);
    }
}
