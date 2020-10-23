<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Database\Seeders;

use Arcanesoft\Foundation\Core\Database\PermissionsSeeder as Seeder;

/**
 * Class     PermissionSeeder
 *
 * @package  Arcanesoft\Media\Database\Seeders
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionSeeder extends Seeder
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
        $this->seed([
            'name'        => 'Media',
            'slug'        => 'media',
            'description' => 'Media permissions group',
        ], $this->getPermissionsFromPolicyManager('admin::media.'));
    }
}
