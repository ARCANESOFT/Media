<?php namespace Arcanesoft\Media\Events;

/**
 * Class     DirectoryDeleting
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DirectoryDeleting
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    public $directory;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DirectoryDeleted constructor.
     *
     * @param  string  $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }
}
