<?php namespace Arcanesoft\Media\Tests;

use Arcanesoft\Media\Tests\Stubs\Models\User;
use Illuminate\Http\UploadedFile;

/**
 * Class     MediaApiTest
 *
 * @package  Arcanesoft\Media\Tests
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

    /**
     * @test
     *
     * @expectedException         \Illuminate\Auth\Access\AuthorizationException
     * @expectedExceptionMessage  [Unauthorized] You are not allowed to perform this action.
     * @expectedExceptionCode     403
     */
    public function it_must_block_access_on_get_all()
    {
        $this->beUser();

        $this->getJson(route('admin::media.api.get'))->json();
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
                     'location' => [
                         'The location field is required.',
                     ],
                     'medias' => [
                         'The medias field is required.',
                     ],
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
                     'name' => [
                         'The name field is required.',
                     ],
                     'location' => [
                         'The location field is required.',
                     ],
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
                     'media'      => [
                         "The media field is required.",
                     ],
                     "media.type" => [
                         "The media.type field is required.",
                     ],
                     "media.name" => [
                         "The media.name field is required.",
                     ],
                     'newName'    => [
                         'The new name field is required.',
                     ],
                     'location'   => [
                         'The location field is required.',
                     ],
                 ],
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
