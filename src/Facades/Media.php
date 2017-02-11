<?php namespace Arcanesoft\Media\Facades;

use Arcanesoft\Media\Contracts\Media as MediaContract;
use Illuminate\Support\Facades\Facade;

/**
 * Class     Media
 *
 * @package  Arcanesoft\Media\Facades
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Media extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return MediaContract::class; }
}
