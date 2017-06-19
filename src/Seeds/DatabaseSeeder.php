<?php namespace Arcanesoft\Media\Seeds;

use Arcanedev\Support\Bases\Seeder;

/**
 * Class     DatabaseSeeder
 *
 * @package  Arcanesoft\Media\Seeds
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DatabaseSeeder extends Seeder
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Seeders collection.
     *
     * @var array
     */
    protected $seeds = [
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
    ];
}
