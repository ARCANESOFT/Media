<?php namespace Arcanesoft\Media\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Class     TestCase
 *
 * @package  Arcanesoft\Media\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class TestCase extends BaseTestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Arcanesoft\Foundation\FoundationServiceProvider::class,
            \Arcanesoft\Auth\AuthServiceProvider::class,
            \Arcanesoft\Media\MediaServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Media' => \Arcanesoft\Media\Facades\Media::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Filesystems
        $app['config']->set(
            'filesystems.disks.media.root',
            realpath(__DIR__.'/fixtures/uploads')
        );

        //Database
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Media settings
        $app['config']->set('arcanesoft.media.directories.excluded', [
            'secret',
        ]);
    }

    /**
     * Get the media manager instance.
     *
     * @return \Arcanesoft\Media\Contracts\Media::class
     */
    protected function media()
    {
        return $this->app->make(\Arcanesoft\Media\Contracts\Media::class);
    }
}
