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
        parent::register();

        $this->registerConfig();
        $this->registerSidebarItems();
        $this->registerProviders([
            CoreServiceProvider::class,
            Providers\PackagesServiceProvider::class,
            Providers\AuthorizationServiceProvider::class,
        ]);
        $this->registerConsoleServiceProvider(Providers\ConsoleServiceProvider::class);

        $this->syncFilesystemConfig();
        $this->registerMediaManager();
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();

        $this->registerProvider(Providers\RouteServiceProvider::class);

        // Publishes
        $this->publishConfig();
        $this->publishViews();
        $this->publishTranslations();
        $this->publishSidebarItems();
        $this->publishAssets();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Contracts\Media::class,
        ];
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Sync the filesystem config.
     */
    private function syncFilesystemConfig()
    {
        foreach ($this->config()->get('arcanesoft.media.filesystem.disks', []) as $disk => $config) {
            $this->config()->set("filesystems.disks.$disk", $config);
        }
    }

    /**
     * Register the media manager.
     */
    private function registerMediaManager()
    {
        $this->singleton(Contracts\Media::class, Media::class);
    }

    /**
     * Publish the assets.
     */
    private function publishAssets()
    {
        $this->publishes([
            $this->getBasePath() . '/resources/assets/js' => resource_path("assets/back/js/components/{$this->vendor}/{$this->package}"),
        ], 'assets-js');
    }
}
