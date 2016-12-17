<?php namespace Arcanesoft\Media\Http\Routes\Admin;

use Arcanedev\Support\Bases\RouteRegister;
use Illuminate\Contracts\Routing\Registrar;

/**
 * Class     MediaRoutes
 *
 * @package  Arcanesoft\Media\Http\Routes
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaRoutes extends RouteRegister
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Map routes.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar $router
     */
    public function map(Registrar $router)
    {
        // media::foundation.index
        $this->get('/', 'MediasController@index')->name('index');

        $this->registerApiRoutes();
    }

    /**
     * Register the media api routes.
     */
    private function registerApiRoutes()
    {
        // TODO: Adding ajax middleware
        $this->group(['prefix' => 'api', 'as' => 'api.'], function () {
            // admin::media.api.get
            $this->get('all', 'MediasController@getAll')->name('get');
            // admin::media.api.upload
            $this->post('upload', 'MediasController@uploadMedia')->name('upload');
            // admin::media.api.delete
            $this->post('rename', 'MediasController@renameMedia')->name('rename');
            // admin::media.api.delete
            $this->post('delete', 'MediasController@deleteMedia')->name('delete');
            // admin::media.api.create
            $this->post('create', 'MediasController@createDirectory')->name('create');
        });
    }
}
