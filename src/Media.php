<?php namespace Arcanesoft\Media;

use Arcanesoft\Media\Contracts\Media as MediaContract;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class     Media
 *
 * @package  Arcanesoft\Media
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Media implements MediaContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Media constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the Filesystem Manager instance.
     *
     * @return \Illuminate\Contracts\Filesystem\Factory
     */
    public function filesystem()
    {
        return $this->app->make('filesystem');
    }

    /**
     * Get the Config Repository.
     *
     * @return \Illuminate\Contracts\Config\Repository
     */
    protected function config()
    {
        return $this->app->make('config');
    }

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
    public function disk($driver = null)
    {
        return $this->filesystem()->disk($driver);
    }

    /**
     * Get the default filesystem adapter.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function defaultDisk()
    {
        return $this->disk(
            $this->config()->get('arcanesoft.media.filesystem.default')
        );
    }
}
