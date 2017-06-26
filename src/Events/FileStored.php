<?php namespace Arcanesoft\Media\Events;

use Illuminate\Http\UploadedFile;

/**
 * Class     UploadedFileStored
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileStored
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Illuminate\Http\UploadedFile */
    public $file;

    /** @var  string */
    public $path;

    /** @var  array */
    public $options;

    /** @var string */
    public $storedPath;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * UploadedFileStoring constructor.
     *
     * @param  string                         $path
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  array                          $options
     * @param  string                         $storedPath
     */
    public function __construct($path, UploadedFile $file, $options, $storedPath)
    {
        $this->path       = $path;
        $this->file       = $file;
        $this->options    = $options;
        $this->storedPath = $storedPath;
    }
}
