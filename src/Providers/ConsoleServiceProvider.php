<?php namespace Arcanesoft\Media\Providers;

use Arcanedev\Support\Providers\CommandServiceProvider as ServiceProvider;
use Arcanesoft\Media\Console;

/**
 * Class     ConsoleServiceProvider
 *
 * @package  Arcanesoft\Media\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ConsoleServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Console commands.
     *
     * @var array
     */
    protected $commands = [
        Console\InstallCommand::class,
        Console\PublishCommand::class,
    ];
}
