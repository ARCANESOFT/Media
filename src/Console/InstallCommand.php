<?php namespace Arcanesoft\Media\Console;

use Arcanesoft\Media\Seeds\DatabaseSeeder;

/**
 * Class     InstallCommand
 *
 * @package  Arcanesoft\Media\Console
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class InstallCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the media module.';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('db:seed', ['--class' => DatabaseSeeder::class]);
    }
}
