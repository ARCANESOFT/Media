<?php namespace Arcanesoft\Media\Events;

/**
 * Class     FileMoving
 *
 * @package  Arcanesoft\Media\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileMoving
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    public $from;

    /** @var string */
    public $to;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * FileMoving constructor.
     *
     * @param  string  $from
     * @param  string  $to
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }
}
