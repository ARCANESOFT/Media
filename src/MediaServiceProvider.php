<?php namespace Arcanesoft\Media;

use Arcanesoft\Core\Bases\PackageServiceProvider;

/**
 * Class     MediaServiceProvider
 *
 * @package  Arcanesoft\Media
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaServiceProvider extends PackageServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'media';

    /* -----------------------------------------------------------------
     |  Main Functions
     | -----------------------------------------------------------------
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
            Providers\AuthorizationServiceProvider::class,
            Providers\RouteServiceProvider::class,
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

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
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
    protected function publishAssets()
    {
        $this->publishes([
            $this->getResourcesPath().DS.'assets'.DS => resource_path("assets/_{$this->vendor}/{$this->package}"),
        ], 'assets');
    }
}
