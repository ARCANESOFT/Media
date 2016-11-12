<?php

use Arcanesoft\Auth\Models\Role;
use Arcanesoft\Auth\Policies;

return [
    'title'       => 'Media',
    'name'        => 'media',
    'route'       => 'media::foundation.index',
    'icon'        => 'fa fa-fw fa-picture-o',
    'roles'       => [Role::ADMINISTRATOR],
    'permissions' => [],
    'children'    => [
        //
    ],
];
