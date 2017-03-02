<?php namespace Arcanesoft\Media\Providers;

use Arcanesoft\Core\Bases\RouteServiceProvider as ServiceProvider;
use Arcanesoft\Media\Http\Routes;

/**
 * Class     RouteServiceProvider
 *
 * @package  Arcanesoft\Media\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RouteServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * The admin controller namespace for the application.
     *
     * @var string
     */
    protected $adminNamespace = 'Arcanesoft\\Media\\Http\\Controllers\\Admin';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Define the routes for the application.
     */
    public function map()
    {
        $this->adminGroup(function () {
            $this->name('media.')->group(function () {
                $this->mapAdminRoutes();
            });
        });
    }

    /**
     * Register the admin routes.
     */
    protected function mapAdminRoutes()
    {
        $prefix = $this->config()->get('arcanesoft.media.route.prefix', 'media');

        $this->prefix($prefix)->group(function () {
            Routes\Admin\MediaRoutes::register();
        });

        $this->prefix("api/{$prefix}")->middleware('ajax')->group(function () {
            Routes\Admin\ApiRoutes::register();
        });
    }
}
