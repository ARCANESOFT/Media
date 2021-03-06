<?php namespace Arcanesoft\Media\Tests\Feature;

use Arcanesoft\Media\Tests\Stubs\Models\User;
use Arcanesoft\Media\Tests\TestCase;
use Illuminate\Http\UploadedFile;

/**
 * Class     MediaApiTest
 *
 * @package  Arcanesoft\Media\Tests\Feature
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaApiTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_get_all_with_default_location()
    {
        $this->beAdmin();

        $resp = $this->getJson(route('admin::media.api.get'));

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
                 'medias' => [
                     [
                         'name' => 'images',
                         'path' => 'images',
                         'type' => 'directory',
                     ],
                 ],
             ]);
    }

    /** @test */
    public function it_can_get_all_with_a_location()
    {
        $this->beAdmin();

        $resp = $this->getJson(route('admin::media.api.get', ['location' => 'images']));

        $resp->assertStatus(200)
             ->assertJson([
                 'status' => 200,
                 'code'   => 'success',
                 'medias' => [
                     [
                         'name' => "avatars",
                         'path' => 'images/avatars',
                         'type' => 'directory',
                     ],[
                         'name' => 'blog',
                         'path' => 'images/blog',
                         'type' => 'directory'
                     ],[
                         'name'         => 'logo.png',
                         'path'         => 'images/logo.png',
                         'url'          => '/uploads/images/logo.png',
                         'mimetype'     => 'image/png',
                         'visibility'   => 'public',
                         'size'         => 7934,
                         'type'         => 'file'
                     ],
                 ],
             ]);
    }

    /** @test */
    public function it_must_block_access_on_get_all()
    {
        $this->beUser();

        $response = $this->getJson(route('admin::media.api.get'));

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => '[Unauthorized] You are not allowed to perform this action.']);
    }

    /** @test */
    public function it_can_upload_media()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.upload'), [
            'location' => 'images/blog',
            'medias'   => [
                UploadedFile::fake()->image('thumbnail.jpg', 800, 400)
            ],
        ]);

        $resp->assertStatus(200)
             ->assertJsonStructure([
                 'status',
                 'code',
                 'data' => [
                     'uploaded' => [
                         "images/blog/thumbnail.jpg",
                     ],
                 ],
             ]);

        // Cleaning
        $media = $this->media();
        foreach ($resp->json()['data']['uploaded'] as $path) {
            $media->deleteFile($path);
        }
    }

    /** @test */
    public function it_must_fail_validation_on_upload_media()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.upload'));

        $resp->assertStatus(422)
             ->assertExactJson([
                 'status'   => 422,
                 'code'     => 'error',
                 'messages' => [
                     'location' => ['The location field is required.'],
                     'medias'   => ['The medias field is required.'],
                 ],
             ]);
    }

    /** @test */
    public function it_can_create_directory()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.create'), [
            'location' => '/',
            'name'     => 'files',
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
                 'data'   => ['path' => '/files']
             ]);

        $this->media()->deleteDirectory('/files');
    }

    /** @test */
    public function it_must_fail_validation_on_create_directory()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.create'), []);

        $resp->assertStatus(422)
             ->assertExactJson([
                 'status'   => 422,
                 'code'     => 'error',
                 'messages' => [
                     'name'     => ['The name field is required.'],
                     'location' => ['The location field is required.'],
                 ],
             ]);
    }

    /** @test */
    public function it_can_rename_directory()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.rename'), [
            'media' => [
                'type' => 'directory',
                'name' => 'images'
            ],
            'newName'  => 'pictures',
            'location' => '/',
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
                 'data'   => [
                     'path'   => '/pictures'
                 ],
             ]);

        $resp = $this->postJson(route('admin::media.api.rename'), [
            'media' => [
                'type' => 'directory',
                'name' => 'pictures'
            ],
            'newName'  => 'images',
            'location' => '/',
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
                 'data'   => [
                     'path'   => '/images'
                 ],
             ]);
    }

    /** @test */
    public function it_can_rename_file()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.rename'), [
            'media' => [
                'type' => 'file',
                'name' => 'logo.png',
            ],
            'newName'  => 'avatar.png',
            'location' => '/images',
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
                 'data'   => [
                     'path'   => 'images/avatar.png'
                 ],
             ]);

        $resp = $this->postJson(route('admin::media.api.rename'), [
            'media' => [
                'type' => 'file',
                'name' => 'avatar.png',
            ],
            'newName'  => 'logo.png',
            'location' => '/images',
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
                 'data'   => [
                     'path'   => 'images/logo.png'
                 ],
             ]);
    }

    /** @test */
    public function it_must_fail_validation_on_rename_directory()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.rename'), []);

        $resp->assertStatus(422)
             ->assertExactJson([
                 'status'   => 422,
                 'code'     => 'error',
                 'messages' => [
                     'media'      => ['The media field is required.'],
                     'media.type' => ['The media.type field is required.'],
                     'media.name' => ['The media.name field is required.'],
                     'newName'    => ['The new name field is required.'],
                     'location'   => ['The location field is required.'],
                 ],
             ]);
    }

    /** @test */
    public function it_must_fail_validation_on_rename_directory_with_invalid_media_type()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.rename'), [
            'media' => [
                'type' => 'link',
                'name' => 'shortcut.lnk'
            ],
            'newName'  => 'photoshop.lnk',
            'location' => '/',
        ]);

        $resp->assertStatus(500)
             ->assertExactJson([
                 'status'  => 500,
                 'code'    => 'error',
                 'message' => 'Something wrong was happened while renaming the media.',
             ]);
    }

    /** @test */
    public function it_can_delete_directory()
    {
        $this->media()->makeDirectory('files');

        $this->assertTrue($this->media()->exists('files'));

        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.delete'), [
            'media' => [
                'type' => 'directory',
                'path' => 'files'
            ],
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
             ]);

        $this->assertFalse($this->media()->exists('files'));
    }

    /** @test */
    public function it_can_delete_file()
    {
        $path = $this->media()->store('/', UploadedFile::fake()->create('file.txt'));

        $this->assertTrue($this->media()->exists($path));

        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.delete'), [
            'media' => [
                'type' => 'file',
                'path' => $path,
            ],
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
             ]);

        $this->assertFalse($this->media()->exists($path));
    }

    /** @test */
    public function it_must_fail_validation_on_delete_media()
    {
        $this->beAdmin();

        $resp = $this->postJson(route('admin::media.api.delete'));

        $resp->assertStatus(422)
             ->assertExactJson([
                 'status'   => 422,
                 'code'     => 'error',
                 'messages' => [
                     'media'      => ['The media field is required.'],
                     'media.path' => ['The media.path field is required.'],
                     'media.type' => ['The media.type field is required.'],
                 ],
             ]);
    }

    /** @test */
    public function it_can_get_move_locations()
    {
        $this->beAdmin();

        $resp = $this->getJson(route('admin::media.api.move-locations'));

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status'       => 200,
                 'code'         => 'success',
                 'destinations' => [
                     'images',
                 ],
             ]);

        $resp = $this->getJson(route('admin::media.api.move-locations', [
            'location' => 'images'
        ]));

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status'       => 200,
                 'code'         => 'success',
                 'destinations' => [
                     '..',
                     'images/avatars',
                     'images/blog',
                 ],
             ]);

        $resp = $this->getJson(route('admin::media.api.move-locations', [
            'location' => 'images',
            'name'     => 'avatars'
        ]));

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status'       => 200,
                 'code'         => 'success',
                 'destinations' => [
                     '..',
                     'images/blog',
                 ],
             ]);
    }

    /** @test */
    public function it_can_move_media()
    {
        $this->beAdmin();

        $this->assertTrue($this->media()->exists('images/logo.png'));
        $this->assertFalse($this->media()->exists('images/avatars/logo.png'));

        $resp = $this->putJson(route('admin::media.api.move'), [
            'old_path' => 'images/logo.png',
            'new_path' => 'images/avatars/logo.png',
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
             ]);

        $this->assertFalse($this->media()->exists('images/logo.png'));
        $this->assertTrue($this->media()->exists('images/avatars/logo.png'));

        $resp = $this->putJson(route('admin::media.api.move'), [
            'old_path' => 'images/avatars/logo.png',
            'new_path' => 'images/logo.png',
        ]);

        $resp->assertStatus(200)
             ->assertExactJson([
                 'status' => 200,
                 'code'   => 'success',
             ]);

        $this->assertTrue($this->media()->exists('images/logo.png'));
        $this->assertFalse($this->media()->exists('images/avatars/logo.png'));
    }

    /** @test */
    public function it_must_fail_validation_on_move_media()
    {
        $this->beAdmin();

        $resp = $this->putJson(route('admin::media.api.move'));

        $resp->assertStatus(422)
             ->assertExactJson([
                 'status'   => 422,
                 'code'     => 'error',
                 'messages' => [
                     'new_path' => ['The new path field is required.'],
                     'old_path' => ['The old path field is required.'],
                 ]
             ]);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make an admin user.
     *
     * @return \Arcanesoft\Media\Tests\Stubs\Models\User
     */
    protected function makeAdminUser()
    {
        return (new User)->forceFill([
            'username'   => 'super.admin',
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'super@admin.com',
            'password'   => 'password',
            'is_admin'   => true,
            'is_active'  => true,
        ]);
    }

    /**
     * Make a normal user.
     *
     * @return \Arcanesoft\Media\Tests\Stubs\Models\User
     */
    protected function makeUser()
    {
        return (new User)->forceFill([
            'username'   => 'j.doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@website.com',
            'password'   => 'password',
            'is_admin'   => false,
            'is_active'  => true,
        ]);
    }

    /**
     * Authenticate as admin.
     */
    protected function beAdmin()
    {
        $this->be($this->makeAdminUser());
    }

    /**
     * Authenticate as a user.
     */
    protected function beUser()
    {
        $this->be($this->makeUser());
    }
}
