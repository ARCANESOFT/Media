<?php namespace Arcanesoft\Media\Providers;

use Arcanesoft\Core\Bases\RouteServiceProvider as ServiceProvider;
use Arcanesoft\Media\Http\Routes;
use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Support\Arr;

/**
 * Class     RouteServiceProvider
 *
 * @package  Arcanesoft\Media\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RouteServiceProvider extends ServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the auth foundation route prefix.
     *
     * @return string
     */
    public function getAdminMediaPrefix()
    {
        $prefix = Arr::get($this->getAdminRouteGroup(), 'prefix', 'dashboard');

        return "$prefix/" . config('arcanesoft.media.route.prefix', 'media');
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     */
    public function map(Router $router)
    {
        $this->mapAdminRoutes($router);
    }

    /**
     * Register the admin routes.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     */
    private function mapAdminRoutes(Router $router)
    {
        $attributes = array_merge($this->getAdminRouteGroup(), [
            'as'        => 'admin::media.',
            'namespace' => 'Arcanesoft\\Media\\Http\\Controllers\\Admin',
        ]);

        $router->group(array_merge(
            $attributes,
            ['prefix' => $this->getAdminMediaPrefix()]
        ), function (Router $router) {
            Routes\Admin\MediaRoutes::register($router);
        });
    }
}
