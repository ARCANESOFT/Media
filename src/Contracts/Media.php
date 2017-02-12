<?php namespace Arcanesoft\Media\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * Interface  Media
 *
 * @package   Arcanesoft\Media\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Media
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */
    const MEDIA_TYPE_DIRECTORY = 'directory';
    const MEDIA_TYPE_FILE      = 'file';

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */
    /**
     * Get the default disk name.
     *
     * @return string
     */
    public function getDefaultDiskName();

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Get a filesystem adapter.
     *
     * @param  string|null  $driver
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public function disk($driver = null);

    /**
     * Get the default filesystem adapter.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public function defaultDisk();

    /**
     * Get all the directories & files from a given location.
     *
     * @param  string  $directory
     *
     * @return array
     */
    public function all($directory);

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string  $directory
     *
     * @return \Arcanesoft\Media\Entities\DirectoryCollection
     */
    public function directories($directory);

    /**
     * Get a collection of all files in a directory.
     *
     * @param  string  $directory
     *
     * @return \Arcanesoft\Media\Entities\FileCollection
     */
    public function files($directory);

    /**
     * Store an array of files.
     *
     * @param  string  $directory
     * @param  array   $files
     */
    public function storeMany($directory, array $files);

    /**
     * Store a file.
     *
     * @param  string                         $directory
     * @param  \Illuminate\Http\UploadedFile  $file
     *
     * @return string|false
     */
    public function store($directory, UploadedFile $file);

    /**
     * Create a directory.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function makeDirectory($path);

    /**
     * Move a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     *
     * @return bool
     */
    public function move($from, $to);

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
    public function exists($path);

    /**
     * Check if the directory is excluded.
     *
     * @param  string  $directory
     *
     * @return bool
     */
    public function isExcludedDirectory($directory);
}
