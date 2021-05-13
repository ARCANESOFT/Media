<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Database\Seeders;

use Arcanesoft\Foundation\Core\Database\RolesSeeder as Seeder;

/**
 * Class     RoleSeeder
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RoleSeeder extends Seeder
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedMany([
            [
                'name'        => 'Media Moderator',
                'key'         => 'media-moderator',
                'description' => 'The media moderator role',
                'is_locked'   => true,
            ],
        ]);

        $this->syncRolesWithPermissions([
            'media-moderator' => [
                'admin::dashboard.index',
                'admin::media.*',
            ],
        ]);
    }
}
