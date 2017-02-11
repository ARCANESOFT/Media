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
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Define the routes for the application.
     */
    public function map()
    {
        $this->mapAdminRoutes();
    }

    /**
     * Register the admin routes.
     */
    protected function mapAdminRoutes()
    {
        $attributes = $this->getAdminAttributes(
            'media.',
            'Arcanesoft\\Media\\Http\\Controllers\\Admin',
            $this->config()->get('arcanesoft.media.route.prefix', 'media')
        );

        $this->group($attributes, function () {
            Routes\Admin\MediaRoutes::register();
            Routes\Admin\ApiRoutes::register(); // TODO: Adding `api` or `ajax` middleware ?
        });
    }
}
