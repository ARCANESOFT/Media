<?php namespace Arcanesoft\Media\Http\Controllers\Admin;

use Arcanedev\LaravelApiHelper\Traits\JsonResponses;
use Arcanesoft\Media\Contracts\Media;
use Arcanesoft\Media\Policies\MediasPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class     ApiController
 *
 * @package  Arcanesoft\Media\Http\Controllers\Admin
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ApiController
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use JsonResponses,
        AuthorizesRequests;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The media instance.
     *
     * @var  \Arcanesoft\Media\Contracts\Media
     */
    protected $media;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * ApiController constructor.
     *
     * @param  \Arcanesoft\Media\Contracts\Media  $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the the media files.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_LIST);

        return $this->jsonResponseSuccess([
            'medias' => $this->media->all(
                $request->get('location', '/')
            ),
        ]);
    }

    /**
     * Upload a media file.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMedia(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_CREATE);

        // TODO: Refactor this with the Laravel 5.5 new Exception Handler & Form Request
        $validator = validator($request->all(), [
            'location' => ['required', 'string'],
            'medias'   => ['required', 'array'],
            'medias.*' => ['required', 'file']
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        $uploaded = $this->media->storeMany(
            $request->get('location'), $request->file('medias')
        );

        return $this->jsonResponseSuccess([
            'data' => compact('uploaded')
        ]);
    }

    /**
     * Create a directory.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDirectory(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_CREATE);

        // TODO: Refactor this with the Laravel 5.5 new Exception Handler & Form Request
        $validator = validator($data = $request->all(), [
            'name'     => ['required', 'string'], // TODO: check if the folder does not exists
            'location' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        $this->media->makeDirectory(
            $path = trim($data['location'], '/').'/'.Str::slug($data['name'])
        );

        return $this->jsonResponseSuccess(['data' => compact('path')]);
    }

    public function renameMedia(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_UPDATE);

        // TODO: Refactor this with the Laravel 5.5 new Exception Handler & Form Request
        // TODO: Check if the folder does not exists
        $validator = validator($data = $request->all(), [
            'media'      => ['required', 'array'],
            'media.type' => ['required', 'string'],
            'media.name' => ['required', 'string'],
            'newName'    => ['required', 'string'],
            'location'   => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        $path = $this->performMoveMedia(
            $data['media']['type'],
            trim($data['location'], '/'),
            $data['media']['name'],
            $data['newName']
        );

        if ($path === false) {
            return $this->jsonResponseError([
                'message' => 'Something wrong was happened while renaming the media.',
            ], 500);
        }
        return $this->jsonResponseSuccess([
            'data' => compact('path'),
        ]);
    }

    public function deleteMedia(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_DELETE);

        // TODO: Refactor this with the Laravel 5.5 new Exception Handler & Form Request
        $validator = validator($data = $request->all(), [
            'media'      => ['required', 'array'],
            'media.type' => ['required', 'string'],
            'media.path' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        return $this->performDeleteMedia($data['media']['type'], $data['media']['path'])
            ? $this->jsonResponseSuccess()
            : $this->jsonResponseError();
    }

    public function moveLocations(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_UPDATE);

        // TODO: Adding validation ?
        $destinations = $this->getDestinations(
            $request->get('name'),
            $request->get('location', '/')
        );

        return $this->jsonResponseSuccess(compact('destinations'));
    }

    public function moveMedia(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_UPDATE);

        // TODO: Refactor this with the Laravel 5.5 new Exception Handler & Form Request
        $validator = validator($data = $request->all(), [
            'old_path' => ['required', 'string'],
            'new_path' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        return $this->media->move($data['old_path'], $data['new_path'])
            ? $this->jsonResponseSuccess()
            : $this->jsonResponseError(['message' => 'Something wrong happened !'], 500);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the destinations paths.
     *
     * @param  string  $name
     * @param  string  $location
     *
     * @return \Arcanesoft\Media\Entities\DirectoryCollection
     */
    private function getDestinations($name, $location)
    {
        $selected     = ($isHome = $location === '/') ? $name : "{$location}/{$name}";
        $destinations = $this->media->directories($location)
            ->pluck('path')
            ->reject(function ($path) use ($selected) {
                return $path === $selected;
            })
            ->values();

        if ( ! $isHome)
            $destinations->prepend('..');

        return $destinations;
    }

    /**
     * Perform the media movement.
     *
     * @param  string  $type
     * @param  string  $location
     * @param  string  $oldName
     * @param  string  $newName
     *
     * @return bool|string
     */
    private function performMoveMedia($type, $location, $oldName, $newName)
    {
        $from = "{$location}/{$oldName}";

        switch (Str::lower($type)) {
            case Media::MEDIA_TYPE_FILE:
                return $this->moveFile($from, $location, $newName);

            case Media::MEDIA_TYPE_DIRECTORY:
                return $this->moveDirectory($from, $location, $newName);

            default:
                return false;
        }
    }

    /**
     * Move file.
     *
     * @param  string  $location
     * @param  string  $location
     * @param  string  $from
     * @param  string  $newName
     *
     * @return string
     */
    private function moveFile($from, $location, $newName)
    {
        $filename  = Str::slug(pathinfo($newName, PATHINFO_FILENAME));
        $extension = pathinfo($newName, PATHINFO_EXTENSION);

        $this->media->move($from, $to = "{$location}/{$filename}.{$extension}");

        return $to;
    }

    /**
     * Move directory.
     *
     * @param  string  $from
     * @param  string  $location
     * @param  string  $newName
     *
     * @return string
     */
    private function moveDirectory($from, $location, $newName)
    {
        $newName = Str::slug($newName);

        return tap("{$location}/{$newName}", function ($to) use ($from) {
            $this->media->move($from, $to);
        });
    }

    /**
     * Perform the media deletion.
     *
     * @param  string  $type
     * @param  string  $path
     *
     * @return bool
     */
    private function performDeleteMedia($type, $path)
    {
        switch (Str::lower($type)) {
            case Media::MEDIA_TYPE_FILE:
                return $this->media->deleteFile($path);

            case Media::MEDIA_TYPE_DIRECTORY:
                return $this->media->deleteDirectory(trim($path, '/'));

            default:
                return false;
        }
    }
}
