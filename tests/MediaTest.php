<?php namespace Arcanesoft\Media\Tests;

/**
 * Class     MediaTest
 *
 * @package  Arcanesoft\Media\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Media\Contracts\Media */
    protected $media;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->media = $this->app->make(\Arcanesoft\Media\Contracts\Media::class);
    }

    public function tearDown()
    {
        unset($this->media);

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
            \Arcanesoft\Media\Contracts\Media::class,
            \Arcanesoft\Media\Media::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->media);
        }
    }

    /** @test */
    public function it_can_get_default_disk()
    {
        $this->assertInstanceOf(
            \Illuminate\Filesystem\FilesystemAdapter::class,
            $this->media->defaultDisk()
        );
    }
}
