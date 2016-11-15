<?php namespace Arcanesoft\Media;

use Arcanesoft\Core\Bases\PackageServiceProvider;
use Arcanesoft\Core\CoreServiceProvider;

/**
 * Class     MediaServiceProvider
 *
 * @package  Arcanesoft\Media
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaServiceProvider extends PackageServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'media';

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the base path of the package.
     *
     * @return string
     */
    public function getBasePath()
    {
        return dirname(__DIR__);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerSidebarItems();
        $this->app->register(CoreServiceProvider::class);
        $this->app->register(Providers\PackagesServiceProvider::class);
        $this->app->register(Providers\AuthorizationServiceProvider::class);

        if ($this->app->runningInConsole()) {
            $this->app->register(Providers\CommandServiceProvider::class);
        }

        $this->syncFilesystemConfig();
    }

    private function syncFilesystemConfig()
    {
        foreach ($this->config()->get('arcanesoft.media.filesystem.disks', []) as $disk => $config) {
            $this->config()->set("filesystems.disks.$disk", $config);
        }
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->app->register(Providers\RouteServiceProvider::class);

        // Publishes
        $this->publishConfig();
        $this->publishViews();
        $this->publishTranslations();
        $this->publishSidebarItems();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            //
        ];
    }
}
