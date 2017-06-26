<?php namespace Arcanesoft\Media\Events;

/**
 * Class     FileMoved
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileMoved
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    public $from;

    /** @var string */
    public $to;

    /** @var bool */
    public $moved;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * FileMoved constructor.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  bool    $moved
     */
    public function __construct($from, $to, $moved)
    {
        $this->from  = $from;
        $this->to    = $to;
        $this->moved = $moved;
    }
}
