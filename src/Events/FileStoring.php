<?php namespace Arcanesoft\Media\Events;

use Illuminate\Http\UploadedFile;

/**
 * Class     UploadedFileStoring
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileStoring
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    public $path;

    /** @var  \Illuminate\Http\UploadedFile */
    public $file;

    /** @var  array */
    public $options;

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
     */
    public function __construct($path, UploadedFile $file, $options)
    {
        $this->path    = $path;
        $this->file    = $file;
        $this->options = $options;
    }
}
