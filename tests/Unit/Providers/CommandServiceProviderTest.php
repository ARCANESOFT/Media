<?php namespace Arcanesoft\Media\Tests\Unit\Providers;

use Arcanesoft\Media\Tests\TestCase;

/**
 * Class     CommandServiceProviderTest
 *
 * @package  Arcanesoft\Media\Tests\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CommandServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Media\Providers\ConsoleServiceProvider */
    private $provider;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(\Arcanesoft\Media\Providers\ConsoleServiceProvider::class);
    }

    public function tearDown()
    {
        unset($this->provider);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Illuminate\Support\ServiceProvider::class,
            \Arcanedev\Support\ServiceProvider::class,
            \Arcanesoft\Media\Providers\ConsoleServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides()
    {
        $expected = [
            \Arcanesoft\Media\Console\InstallCommand::class,
            \Arcanesoft\Media\Console\PublishCommand::class,
        ];

        $this->assertSame($expected, $this->provider->provides());
    }
}
