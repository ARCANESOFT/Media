<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Policies;

use Arcanesoft\Foundation\Auth\Models\Administrator;
use Arcanesoft\Foundation\Support\Auth\Policy;

/**
 * Class     MediaPolicy
 *
 * @package  Arcanesoft\Media\Policies
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaPolicy extends Policy
{
    /* -----------------------------------------------------------------
     |  Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get the ability's prefix.
     *
     * @return string
     */
    protected static function prefix(): string
    {
        return 'admin::media.';
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the policy's abilities.
     *
     * @return \Arcanedev\LaravelPolicies\Ability[]|iterable
     */
    public function abilities(): iterable
    {
        $this->setMetas([
            'category' => 'Media',
        ]);

        return [

            // admin::media.index
            $this->makeAbility('index')->setMetas([
                'name'        => 'Access the main media manager',
                'description' => 'Ability to access the main media manager',
            ]),

        ];
    }

    /* -----------------------------------------------------------------
     |  Policies
     | -----------------------------------------------------------------
     */

    /**
     * Allow to access the main media manager.
     *
     * @param  \Arcanesoft\Foundation\Auth\Models\Administrator|mixed  $administrator
     *
     * @return \Illuminate\Auth\Access\Response|bool|void
     */
    public function index(Administrator $administrator)
    {
        //
    }
}
