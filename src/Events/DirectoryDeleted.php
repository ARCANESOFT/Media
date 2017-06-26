<?php namespace Arcanesoft\Media\Events;

/**
 * Class     DirectoryDeleted
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DirectoryDeleted
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    public $directory;

    /** @var  bool */
    public $deleted;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DirectoryDeleted constructor.
     *
     * @param  string  $directory
     * @param  bool    $deleted
     */
    public function __construct($directory, $deleted)
    {
        $this->directory = $directory;
        $this->deleted   = $deleted;
    }
}
