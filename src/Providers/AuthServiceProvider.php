<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Providers;

use Arcanesoft\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Class     AuthServiceProvider
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AuthServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get policy's classes.
     *
     * @return iterable
     */
    public function policyClasses(): iterable
    {
        return $this->app['config']->get('arcanesoft.media.policies', []);
    }
}
