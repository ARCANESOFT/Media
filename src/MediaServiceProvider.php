<?php

declare(strict_types=1);

namespace Arcanesoft\Media;

use Arcanesoft\Foundation\Support\Providers\PackageServiceProvider;

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
     * The package name.
     *
     * @var  string
     */
    protected $package = 'media';

    /**
     * Merge multiple config files into one instance (package name as root key).
     *
     * @var bool
     */
    protected $multiConfigs = true;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerConfig();

        $this->registerProviders([
            Providers\AuthServiceProvider::class,
            Providers\RouteServiceProvider::class,
        ]);

        $this->app->booting(function ($app) {
            /** @var  \Illuminate\Contracts\Config\Repository  $config */
            $config = $app['config'];

            $config->set('filesystems.disks', array_merge(
                $config->get('arcanesoft.media.filesystems.disks', []),
                $config->get('filesystems.disks', [])
            ));
        });
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->loadTranslations();
        $this->loadViews();

        if ($this->app->runningInConsole()) {
            $this->publishAssets();
            $this->publishConfig();
            $this->publishTranslations();
            $this->publishViews();
        }
    }
}
