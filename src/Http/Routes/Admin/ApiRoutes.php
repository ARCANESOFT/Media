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
        $this->prefix('api')->name('api.')->group(function () {
            $this->get('all', 'MediasController@getAll')
                 ->name('get');    // admin::media.api.get

            $this->post('upload', 'MediasController@uploadMedia')
                 ->name('upload'); // admin::media.api.upload

            $this->post('rename', 'MediasController@renameMedia')
                 ->name('rename'); // admin::media.api.rename

            $this->post('delete', 'MediasController@deleteMedia')
                 ->name('delete'); // admin::media.api.delete

            $this->post('create', 'MediasController@createDirectory')
                 ->name('create'); // admin::media.api.create
        });
    }
}
