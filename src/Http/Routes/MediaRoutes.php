<?php

namespace Arcanesoft\Media\Http\Routes;

use Arcanesoft\Foundation\Support\Http\AdminRouteRegistrar;
use Arcanesoft\Media\Http\Controllers\{MediaApiController, MediaController};

/**
 * Class     MediaRoutes
 *
 * @package  Arcanesoft\Media\Http\Routes
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaRoutes extends AdminRouteRegistrar
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Map the routes.
     */
    public function map(): void
    {
        $this->adminGroup(function () {
            $this->prefix('media')->name('media.')->group(function () {
                $this->get('/', [MediaController::class, 'index'])
                     ->name('index'); // admin::media.index

                $this->mapApiRoutes();
            });
        });
    }

    /**
     * Map Media API Routes.
     */
    private function mapApiRoutes(): void
    {
        $this->prefix('api')->name('api.')->middleware(['ajax'])->group(function () {
            $this->get('items', [MediaApiController::class, 'all'])
                 ->name('items.index'); // admin::media.api.items.index

            $this->get('directories', [MediaApiController::class, 'directories'])
                 ->name('directories.index'); // admin::media.api.directories.index

            $this->post('upload', [MediaApiController::class, 'upload'])
                 ->name('upload'); // admin::media.api.upload

            $this->post('new-folder', [MediaApiController::class, 'newFolder'])
                 ->name('new-folder'); // admin::media.api.new-folder

            $this->put('move', [MediaApiController::class, 'move'])
                 ->name('move'); // admin::media.api.move

            $this->put('rename', [MediaApiController::class, 'rename'])
                 ->name('rename'); // admin::media.api.rename

            $this->delete('delete', [MediaApiController::class, 'delete'])
                 ->name('delete'); // admin::media.api.delete

            $this->get('download', [MediaApiController::class, 'download'])
                 ->name('download'); // admin::media.api.delete
        });
    }
}
