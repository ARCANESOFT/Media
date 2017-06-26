<?php namespace Arcanesoft\Media\Events;

/**
 * Class     DirectoryCreating
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DirectoryCreating
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    public $path;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DirectoryCreating constructor.
     *
     * @param  string  $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }
}
