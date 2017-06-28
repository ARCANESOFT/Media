<?php namespace Arcanesoft\Media\Tests\Unit;

use Arcanesoft\Media\Events;
use Arcanesoft\Media\Tests\TestCase;
use Illuminate\Http\UploadedFile;

/**
 * Class     MediaTest
 *
 * @package  Arcanesoft\Media\Tests\Unit
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

        $this->media = $this->media();
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
            $this->media->disk()
        );

        $this->assertSame('media', $this->media->getDefaultDiskName());
    }

    /** @test */
    public function it_can_get_excluded_directories()
    {
        $this->app['config']->set('arcanesoft.media.directories.excluded', [
            'secret/folder',
        ]);

        $this->assertSame([
            'secret/folder',
            'secret/folder/*',
        ], $this->media->getExcludedDirectories());
    }

    /** @test */
    public function it_can_get_excluded_files()
    {
        $this->assertSame([
            '.gitignore',
            '.gitkeep',
        ], $this->media->getExcludedFiles());
    }

    /** @test */
    public function it_can_get_all_directories()
    {
        $directories = $this->media->directories('/');

        $expected = [
            ['name' => 'images', 'path' => 'images'],
        ];

        $this->assertSame(count($expected), $directories->count());
        $this->assertSame($expected, $directories->toArray());
    }

    /**
     * @test
     *
     * @expectedException         \Arcanesoft\Media\Exceptions\DirectoryNotFound
     * @expectedExceptionMessage  Directory [#swag] not found !
     */
    public function it_must_throw_exception_if_directory_not_found()
    {
        $this->media->directories('#swag');
    }

    /**
     * @test
     *
     * @expectedException         \Arcanesoft\Media\Exceptions\AccessNotAllowed
     * @expectedExceptionMessage  Access not allowed.
     */
    public function it_must_throw_exception_if_directory_is_ignored()
    {
        $this->media->directories('secret');
    }

    /** @test */
    public function it_can_get_all()
    {
        $medias = $this->media->all();

        $expected = [
            ['name' => 'images', 'path' => 'images', 'type' => 'directory'],
        ];

        $this->assertSame(count($expected), count($medias));
        $this->assertSame($expected, $medias);

        $medias = $this->media->all('images');

        $expected = [
            [
                'name' => 'avatars',
                'path' => 'images/avatars',
                'type' => 'directory',
            ],
            [
                'name' => 'blog',
                'path' => 'images/blog',
                'type' => 'directory',
            ],
            [
                'name'         => 'logo.png',
                'path'         => 'images/logo.png',
                'url'          => '/uploads/images/logo.png',
                'mimetype'     => 'image/png',
                'visibility'   => 'public',
                'size'         => 7934,
                'type'         => 'file',
            ],
        ];

        $this->assertSame(count($expected), count($medias));
        $this->assertArraySubset($expected, $medias);
    }

    /** @test */
    public function it_can_make_and_delete_directory()
    {
        $this->assertEvents([
            Events\DirectoryCreating::class,
            Events\DirectoryCreated::class,
            Events\DirectoryDeleting::class,
            Events\DirectoryDeleted::class,
        ]);

        $this->assertTrue($this->media->makeDirectory('tmp'));

        $this->assertTrue($this->media->exists('tmp'));
        $this->assertTrue($this->media->deleteDirectory('tmp'));

        $this->assertFalse($this->media->exists('tmp'));
        $this->assertFalse($this->media->deleteDirectory('tmp'));
    }

    /** @test */
    public function it_can_store_and_get_and_delete_file()
    {
        $this->assertEvents([
            Events\FileStoring::class,
            Events\FileStored::class,
            Events\FileDeleting::class,
            Events\FileDeleted::class,
        ]);

        $path = $this->media->store(
            'images/avatars',
            UploadedFile::fake()->image('user-1.png', 64, 64)
        );

        $this->assertTrue($this->media->exists($path));

        $file = $this->media->file($path);
        $this->assertArraySubset([
            'path'       => $path,
            'url'        => "/uploads/{$path}",
            'mimetype'   => 'image/png',
            'visibility' => 'public',
            'size'       => 91,
        ], $file);

        $this->assertArrayHasKey('name', $file);
        $this->assertArrayHasKey('lastModified', $file);

        $this->assertTrue($this->media->deleteFile($path));
        $this->assertFalse($this->media->exists($path));
    }

    /** @test */
    public function it_can_store_many_files()
    {
        $this->assertEvents([
            Events\DirectoryCreating::class,
            Events\DirectoryCreated::class,
            Events\DirectoryDeleting::class,
            Events\DirectoryDeleted::class,
            Events\FileStoring::class,
            Events\FileStored::class,
        ]);

        $this->media->makeDirectory('files');

        $this->assertCount(0, $this->media->files('files'));

        $uploaded = $this->media->storeMany('files', [
            UploadedFile::fake()->create('file-1.txt'),
            UploadedFile::fake()->create('file-2.txt'),
            UploadedFile::fake()->create('file-3.txt'),
        ]);

        $this->assertCount(count($uploaded), $this->media->files('files'));

        $this->media->deleteDirectory('files');
    }

    /** @test */
    public function it_can_move_file()
    {
        $this->assertEvents([
            Events\FileMoving::class,
            Events\FileMoved::class,
        ]);

        $this->media->move('images/logo.png', 'images/avatars/logo.png');

        $this->assertCount(0, $this->media->files('images'));
        $this->assertCount(1, $this->media->files('images/avatars'));

        $this->media->move('images/avatars/logo.png', 'images/logo.png');

        $this->assertCount(1, $this->media->files('images'));
        $this->assertCount(0, $this->media->files('images/avatars'));
    }

    /**
     * @test
     *
     * @expectedException         \Arcanesoft\Media\Exceptions\FileNotFoundException
     * @expectedExceptionMessage  File [images/dank-meme.jpeg] not found!
     */
    public function it_must_throw_exception_while_getting_a_file_that_does_not_exist()
    {
        $this->media->file('images/dank-meme.jpeg');
    }

    /* -----------------------------------------------------------------
     |  Other methods
     | -----------------------------------------------------------------
     */

    /**
     * Assert the events.
     *
     * @param  array  $events
     */
    protected function assertEvents(array $events)
    {
        $all = [
            Events\DirectoryCreating::class,
            Events\DirectoryCreated::class,
            Events\DirectoryDeleting::class,
            Events\DirectoryDeleted::class,
            Events\FileDeleted::class,
            Events\FileDeleting::class,
            Events\FileMoving::class,
            Events\FileMoved::class,
            Events\FileStoring::class,
            Events\FileStored::class,
        ];

        $this->expectsEvents($events);
        $this->doesntExpectEvents(array_diff($all, $events));
    }
}
