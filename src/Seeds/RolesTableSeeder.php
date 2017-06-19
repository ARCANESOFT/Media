<?php namespace Arcanesoft\Media\Seeds;

use Arcanesoft\Auth\Seeds\RolesSeeder;

/**
 * Class     RolesTableSeeder
 *
 * @package  Arcanesoft\Media\Seeds
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RolesTableSeeder extends RolesSeeder
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
                'name'        => 'Medias Manager',
                'description' => 'The Medias manager role.',
                'is_locked'   => true,
            ],
        ]);

        $this->syncAdminRole();

        $this->syncRoles([
            'medias-manager' => 'media.medias.',
        ]);
    }
}
