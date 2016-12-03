<?php namespace Arcanesoft\Media\Console;

use Arcanesoft\Media\MediaServiceProvider;

/**
 * Class     PublishCommand
 *
 * @package  Arcanesoft\Media\Console
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PublishCommand extends Command
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish media config, assets and other stuff.';

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => MediaServiceProvider::class,
        ]);
    }
}
