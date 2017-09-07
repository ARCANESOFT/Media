<?php namespace Arcanesoft\Media\Http\Routes\Admin;

use Arcanedev\Support\Routing\RouteRegistrar;

/**
 * Class     MediaRoutes
 *
 * @package  Arcanesoft\Media\Http\Routes
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaRoutes extends RouteRegistrar
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Map routes.
     */
    public function map()
    {
        $this->get('/', 'MediasController@index')
             ->name('index'); // admin::media.index
    }
}
