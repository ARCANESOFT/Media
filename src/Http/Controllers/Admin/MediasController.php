<?php namespace Arcanesoft\Media\Http\Controllers\Admin;

use Arcanedev\LaravelApiHelper\Traits\JsonResponses;
use Arcanesoft\Media\Contracts\Media;
use Arcanesoft\Media\Policies\MediasPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class     MediasController
 *
 * @package  Arcanesoft\Media\Http\Controllers\Admin
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @TODO: Extract all the validators to FormRequest
 */
class MediasController extends Controller
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use JsonResponses;

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
     * MediasController constructor.
     *
     * @param  \Arcanesoft\Media\Contracts\Media  $media
     */
    public function __construct(Media $media)
    {
        parent::__construct();

        $this->media = $media;

        $this->setCurrentPage('media');
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Show the media manager page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->authorize(MediasPolicy::PERMISSION_LIST);

        $this->setTitle('Media');

        return $this->view('admin.manager');
    }

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
            'location' => 'required|string',
            'medias'   => 'required|array',
            'medias.*' => 'required|file'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        $this->media->storeMany(
            $request->get('location'), $request->file('medias')
        );

        return $this->jsonResponseSuccess();
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
            'name'     => 'required|string', // TODO: check if the folder does not exists
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseError([
                'messages' => $validator->messages(),
            ], 422);
        }

        $this->media->makeDirectory(
            $path = trim($data['location'], '/').'/'.Str::slug($data['name'])
        );

        return $this->jsonResponseSuccess(compact('path'));
    }

    public function renameMedia(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_UPDATE);

        $data = $request->all();

        // TODO: check if the folder does not exists
        $validator = validator($data, [
            'media'    => 'required|array',
            'newName'  => 'required|string',
            'location' => 'required|string',
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
                return $this->jsonResponseSuccess([
                    'path' => $this->moveFile($location, $from, $data)
                ]);

            case Media::MEDIA_TYPE_DIRECTORY:
                return $this->jsonResponseSuccess([
                    'path' => $this->moveDirectory($location, $from, $data)
                ]);

            default:
                $this->jsonResponseError([
                    'message' => 'Something wrong was happened while renaming the media.',
                ]);
        }
    }

    public function deleteMedia(Request $request)
    {
        $this->authorize(MediasPolicy::PERMISSION_DELETE);

        // TODO: Add validation
        $data = $request->all();
        $disk = $this->media->defaultDisk();

        // TODO Refactor this...
        if ($data['media']['type'] == Media::MEDIA_TYPE_FILE) {
            $deleted = $disk->delete($data['media']['path']);
        }
        else {
            $deleted = $disk->deleteDirectory(
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
     * @param  array   $data
     *
     * @return string
     */
    private function moveFile($location, $from, array $data)
    {
        $ext = pathinfo($data['media']['url'], PATHINFO_EXTENSION);
        $to  = $location.'/'.Str::slug(str_replace(".{$ext}", '', $data['newName'])).'.'.$ext;

        $this->media->move($from, $to);

        return $to;
    }

    /**
     * Move a directory.
     *
     * @param  string  $location
     * @param  string  $from
     * @param  array   $data
     *
     * @return string
     */
    private function moveDirectory($location, $from, array $data)
    {
        $to = $location.'/'.Str::slug($data['newName']);

        $this->media->move($from, $to);

        return $to;
    }
}
