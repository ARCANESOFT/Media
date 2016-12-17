<?php

use Arcanesoft\Auth\Models\Role;
use Arcanesoft\Auth\Policies;

return [
    'title'       => 'Media',
    'name'        => 'media',
    'route'       => 'admin::media.index',
    'icon'        => 'fa fa-fw fa-picture-o',
    'roles'       => [Role::ADMINISTRATOR],
    'permissions' => [],
    'children'    => [
        //
    ],
];
