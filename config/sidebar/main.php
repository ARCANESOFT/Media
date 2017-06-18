<?php

use Arcanesoft\Auth\Models\Role;
use Arcanesoft\Media\Policies\MediasPolicy;

return [
    'title'       => 'Media',
    'name'        => 'media',
    'route'       => 'admin::media.index',
    'icon'        => 'fa fa-fw fa-picture-o',
    'roles'       => [Role::ADMINISTRATOR],
    'permissions' => [
        MediasPolicy::PERMISSION_LIST,
    ],
    'children'    => [
        //
    ],
];
