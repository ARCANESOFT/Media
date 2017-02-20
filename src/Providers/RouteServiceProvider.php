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
            $this->mapAdminRoutes();
        });
    }

    /**
     * Register the admin routes.
     */
    protected function mapAdminRoutes()
    {
        $this->name('media.')
             ->prefix($this->config()->get('arcanesoft.media.route.prefix', 'media'))
             ->group(function () {
                 Routes\Admin\MediaRoutes::register();
                 Routes\Admin\ApiRoutes::register(); // TODO: Adding `api` or `ajax` middleware ?
             });
    }
}
