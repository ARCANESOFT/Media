<?php namespace Arcanesoft\Media\Providers;

use Arcanesoft\Core\Bases\RouteServiceProvider as ServiceProvider;
use Arcanesoft\Media\Http\Routes;
use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Support\Arr;

class RouteServiceProvider extends ServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the routes namespace
     *
     * @return string
     */
    protected function getRouteNamespace()
    {
        return 'Arcanesoft\\Media\\Http\\Routes';
    }

    /**
     * Get the auth foundation route prefix.
     *
     * @return string
     */
    public function getFoundationAuthPrefix()
    {
        $prefix = Arr::get($this->getFoundationRouteGroup(), 'prefix', 'dashboard');

        return "$prefix/" . config('arcanesoft.media.route.prefix', 'media');
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar $router
     */
    public function map(Router $router)
    {
        $this->mapFoundationRoutes($router);
    }

    private function mapFoundationRoutes(Router $router)
    {
        $attributes = array_merge($this->getFoundationRouteGroup(), [
            'as'        => 'media::foundation.',
            'namespace' => 'Arcanesoft\\Media\\Http\\Controllers',
        ]);

        $router->group(array_merge(
            $attributes,
            ['prefix' => $this->getFoundationAuthPrefix()]
        ), function (Router $router) {
            Routes\MediaRoutes::register($router);
        });
    }
}
