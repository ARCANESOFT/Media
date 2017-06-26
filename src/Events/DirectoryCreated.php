<?php namespace Arcanesoft\Media\Events;

/**
 * Class     DirectoryCreated
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DirectoryCreated
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    public $path;

    /** @var  bool */
    public $created;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DirectoryCreated constructor.
     *
     * @param  string  $path
     * @param  bool    $created
     */
    public function __construct($path, $created)
    {
        $this->path    = $path;
        $this->created = $created;
    }
}
