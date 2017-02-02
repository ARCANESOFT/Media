<?php namespace Arcanesoft\Media\Contracts;

/**
 * Interface  Media
 *
 * @package   Arcanesoft\Media\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Media
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get a filesystem adapter.
     *
     * @param  string|null  $driver
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($driver = null);

    /**
     * Get the default filesystem adapter.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function defaultDisk();
}
