<?php namespace Arcanesoft\Media\Providers;

use Arcanedev\Support\Providers\AuthorizationServiceProvider as ServiceProvider;
use Arcanesoft\Media\Policies\MediasPolicy;

/**
 * Class     AuthorizationServiceProvider
 *
 * @package  Arcanesoft\Media\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AuthorizationServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register any application authentication / authorization services.
     */
    public function boot()
    {
        parent::registerPolicies();

        $this->defineMany(MediasPolicy::class, MediasPolicy::policies());
    }
}
