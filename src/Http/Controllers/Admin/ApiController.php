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

        $location = $request->get('location', '/');

        return $this->jsonResponseSuccess([
            'medias' => $this->media->all($location),
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

        $data      = $request->all();
        $validator = validator($data, [
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

        // TODO: check if the folder does not exists
        $validator = validator($data = $request->all(), [
            'media'      => ['required', 'array'],
            'media.type' => ['required', 'string', 'in:'.implode(',', [Media::MEDIA_TYPE_FILE, Media::MEDIA_TYPE_DIRECTORY])],
            'media.name' => ['required', 'string'],
            'newName'    => ['required', 'string'],
            'location'   => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        // TODO Refactor this...
        $location = trim($data['location'], '/');
        $from     = $location.'/'.$data['media']['name'];

        switch ($data['media']['type']) {
            case Media::MEDIA_TYPE_FILE:
                $path = $this->moveFile($location, $from, $data['newName']);
                break;

            case Media::MEDIA_TYPE_DIRECTORY:
                $path = $this->moveDirectory($location, $from, $data['newName']);
                break;

            default:
                return $this->jsonResponseError([
                    'message' => 'Something wrong was happened while renaming the media.',
                ]);
        }

        return $this->jsonResponseSuccess([
            'data' => compact('path'),
        ]);
    }

    public function deleteMedia(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_DELETE);

        // TODO: Add validation
        $data = $request->all();

        // TODO Refactor this...
        if ($data['media']['type'] == Media::MEDIA_TYPE_FILE) {
            $deleted = $this->media->deleteFile($data['media']['path']);
        }
        else {
            $deleted = $this->media->deleteDirectory(
                trim($data['media']['path'], '/')
            );
        }

        return $deleted ? $this->jsonResponseSuccess() : $this->jsonResponseError();
    }

    public function moveLocations(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_UPDATE);

        $location  = $request->get('location');
        $name      = $request->get('name');
        $isHome    = $location == '/';
        $selected  = $isHome ? $name : $location.'/'.$name;

        /** @var \Illuminate\Support\Collection $destinations */
        $destinations = $this->media->directories($location)
            ->transform(function ($directory) {
                return $directory['path'];
            })
            ->reject(function ($path) use ($selected) {
                return $path === $selected;
            })
            ->values();

        if ( ! $isHome) {
            $destinations->prepend('..');
        }

        return $this->jsonResponseSuccess([
            'destinations' => $destinations,
        ]);
    }

    public function moveMedia(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_UPDATE);

        $validator = validator($data = $request->all(), [
            'old-path' => ['required', 'string'],
            'new-path' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        return $this->media->move($data['old-path'], $data['new-path'])
            ? $this->jsonResponseSuccess()
            : $this->jsonResponseError(['message' => 'Something wrong happened !'], 500);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Move a file.
     *
     * @param  string  $location
     * @param  string  $from
     * @param  string  $newName
     *
     * @return string
     */
    private function moveFile($location, $from, $newName)
    {
        $filename = Str::slug(pathinfo($newName, PATHINFO_FILENAME)).'.'.pathinfo($newName, PATHINFO_EXTENSION);

        $this->media->move($from, $to = $location.'/'.$filename);

        return $to;
    }

    /**
     * Move a directory.
     *
     * @param  string  $location
     * @param  string  $from
     * @param  string  $newName
     *
     * @return string
     */
    private function moveDirectory($location, $from, $newName)
    {
        $to = $location.'/'.Str::slug($newName);

        $this->media->move($from, $to);

        return $to;
    }
}
