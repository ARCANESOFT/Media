<?php namespace Arcanesoft\Media\Events;

/**
 * Class     FileDeleted
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileDeleted
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    public $path;

    /** @var  bool */
    public $deleted;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * FileDeleted constructor.
     *
     * @param  string  $path
     * @param  bool    $deleted
     */
    public function __construct($path, $deleted)
    {
        $this->path    = $path;
        $this->deleted = $deleted;
    }
}
