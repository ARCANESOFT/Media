<?php namespace Arcanesoft\Media\Seeds;

use Arcanesoft\Auth\Seeds\PermissionsSeeder;
use Arcanesoft\Media\Policies\MediasPolicy;

/**
 * Class     PermissionsTableSeeder
 *
 * @package  Arcanesoft\Media\Seeds
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionsTableSeeder extends PermissionsSeeder
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->seed([
            [
                'group'       => [
                    'name'        => 'Media',
                    'slug'        => 'media',
                    'description' => 'Media permissions group',
                ],
                'permissions' => array_merge(
                    $this->getMediasPermissions()
                ),
            ],
        ]);
    }

    /* -----------------------------------------------------------------
     |  Permissions
     | -----------------------------------------------------------------
     */

    /**
     * Get the Medias permissions.
     *
     * @return array
     */
    private function getMediasPermissions()
    {
        return [
            [
                'name'        => 'Medias - List all medias',
                'description' => 'Allow to list all the medias.',
                'slug'        => MediasPolicy::PERMISSION_LIST,
            ],
            [
                'name'        => 'Medias - View a media',
                'description' => 'Allow to display a media.',
                'slug'        => MediasPolicy::PERMISSION_SHOW,
            ],
            [
                'name'        => 'Medias - Download a media',
                'description' => 'Allow to download a media.',
                'slug'        => MediasPolicy::PERMISSION_CREATE,
            ],
            [
                'name'        => 'Medias - Update a media',
                'description' => 'Allow to update a media.',
                'slug'        => MediasPolicy::PERMISSION_UPDATE,
            ],
            [
                'name'        => 'Medias - Delete a media',
                'description' => 'Allow to delete a media.',
                'slug'        => MediasPolicy::PERMISSION_DELETE,
            ],
        ];
    }
}
