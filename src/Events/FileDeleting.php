<?php namespace Arcanesoft\Media\Events;

/**
 * Class     FileDeleting
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileDeleting
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    public $path;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * FileDeleting constructor.
     *
     * @param  string  $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }
}
