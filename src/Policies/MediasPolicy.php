<?php namespace Arcanesoft\Media\Policies;

use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     MediaPolicy
 *
 * @package  Arcanesoft\Media\Policies
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediasPolicy extends AbstractPolicy
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    const PERMISSION_LIST   = 'media.medias.list';
    const PERMISSION_SHOW   = 'media.medias.show';
    const PERMISSION_CREATE = 'media.medias.create';
    const PERMISSION_UPDATE = 'media.medias.update';
    const PERMISSION_DELETE = 'media.medias.delete';

    /* -----------------------------------------------------------------
     |  Abilities
     | -----------------------------------------------------------------
     */

    /**
     * Allow to list all the medias.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return bool
     */
    public function listPolicy(User $user)
    {
        return $user->may(static::PERMISSION_LIST);
    }

    /**
     * Allow to view a medias.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return bool
     */
    public function showPolicy(User $user)
    {
        return $user->may(static::PERMISSION_SHOW);
    }

    /**
     * Allow to create a media.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return bool
     */
    public function createPolicy(User $user)
    {
        return $user->may(static::PERMISSION_CREATE);
    }

    /**
     * Allow to update a media.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return bool
     */
    public function updatePolicy(User $user)
    {
        return $user->may(static::PERMISSION_UPDATE);
    }

    /**
     * Allow to delete a media.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return bool
     */
    public function deletePolicy(User $user)
    {
        return $user->may(static::PERMISSION_DELETE);
    }
}
