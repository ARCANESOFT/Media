<?php namespace Arcanesoft\Media\Http\Routes;

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

    private function registerApiRoutes()
    {
        // TODO: Adding ajax middleware
        $this->group(['prefix' => 'api', 'as' => 'api.'], function () {
            // media::foundation.api.get
            $this->get('all', 'MediasController@getAll')->name('get');
            // media::foundation.api.upload
            $this->post('upload', 'MediasController@uploadMedia')->name('upload');
            // media::foundation.api.delete
            $this->post('rename', 'MediasController@renameMedia')->name('rename');
            // media::foundation.api.delete
            $this->post('delete', 'MediasController@deleteMedia')->name('delete');

            // media::foundation.api.create
            $this->post('create', 'MediasController@createDirectory')->name('create');
        });
    }
}
