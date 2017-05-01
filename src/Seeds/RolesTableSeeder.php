<?php namespace Arcanesoft\Media\Seeds;

use Arcanesoft\Auth\Models\Permission;
use Arcanesoft\Auth\Models\Role;
use Arcanesoft\Auth\Seeds\RolesSeeder;
use Illuminate\Support\Str;

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

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Sync the roles.
     *
     * @todo: Refactor this method
     *
     * @param  array  $roles
     */
    protected function syncRoles(array $roles)
    {
        /** @var \Illuminate\Database\Eloquent\Collection $permissions */
        $permissions = Permission::all();

        foreach ($roles as $roleSlug => $permissionSlug) {
            /** @var  \Arcanesoft\Auth\Models\Role  $role */
            $role = Role::where('slug', $roleSlug)->first();
            $ids  = $permissions->filter(function (Permission $permission) use ($permissionSlug) {
                return Str::startsWith($permission->slug, $permissionSlug);
            })->pluck('id')->toArray();

            $role->permissions()->sync($ids);
        }
    }
}
