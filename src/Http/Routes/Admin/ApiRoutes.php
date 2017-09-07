<?php namespace Arcanesoft\Media\Http\Routes\Admin;

use Arcanedev\Support\Routing\RouteRegistrar;

/**
 * Class     ApiRoutes
 *
 * @package  Arcanesoft\Media\Http\Routes\Admin
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ApiRoutes extends RouteRegistrar
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
        $this->prefix('api')->name('api.')->middleware('ajax')->group(function () {
            $this->get('all', 'ApiController@getAll')
                 ->name('get');    // admin::media.api.get

            $this->post('upload', 'ApiController@uploadMedia')
                 ->name('upload'); // admin::media.api.upload

            $this->post('rename', 'ApiController@renameMedia')
                 ->name('rename'); // admin::media.api.rename

            $this->post('delete', 'ApiController@deleteMedia')
                 ->name('delete'); // admin::media.api.delete

            $this->get('move-locations', 'ApiController@moveLocations')
                 ->name('move-locations'); // admin::media.api.move-locations

            $this->put('move', 'ApiController@moveMedia')
                 ->name('move'); // admin::media.api.move

            $this->post('create', 'ApiController@createDirectory')
                 ->name('create'); // admin::media.api.create
        });
    }
}
