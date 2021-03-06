<?php namespace Arcanesoft\Media\Tests\Unit;

use Arcanesoft\Media\Tests\TestCase;

/**
 * Class     MediaServiceProviderTest
 *
 * @package  Arcanesoft\Media\Tests\Unit
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Media\MediaServiceProvider */
    protected $provider;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(\Arcanesoft\Media\MediaServiceProvider::class);
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
            \Arcanedev\Support\PackageServiceProvider::class,
            \Arcanesoft\Core\Bases\PackageServiceProvider::class,
            \Arcanesoft\Media\MediaServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides()
    {
        $expected = [
            \Arcanesoft\Media\Contracts\Media::class,
        ];

        $this->assertSame($expected, $this->provider->provides());
    }
}
