<?php

use Arcanesoft\Auth\Models\Role;

return [
    'title'       => 'Media',
    'name'        => 'media',
    'route'       => 'admin::media.index',
    'icon'        => 'fa fa-fw fa-picture-o',
    'roles'       => [Role::ADMINISTRATOR],
    'permissions' => [
        Arcanesoft\Media\Policies\MediasPolicy::PERMISSION_LIST,
    ],
    'children'    => [
        //
    ],
];
