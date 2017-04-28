<?php namespace Arcanesoft\Media\Http\Controllers\Admin;

use Arcanesoft\Media\Contracts\Media;
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
        $location = $request->get('location', '/');

        return response()->json([
            'status' => 'success',
            'data'   => $this->media->all($location),
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
        $validator = validator($request->all(), [
            'location' => 'required|string',
            'medias'   => 'required|array',
            'medias.*' => 'required|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
            ]);
        }

        $this->media->storeMany(
            $request->get('location'), $request->file('medias')
        );

        return response()->json(['status' => 'success']);
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
        $data      = $request->all();
        $validator = validator($data, [
            'name'     => 'required|string', // TODO: check if the folder does not exists
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
            ], 400);
        }

        $this->media->makeDirectory(
            $path = trim($data['location'], '/').'/'.Str::slug($data['name'])
        );

        return response()->json([
            'status' => 'success',
            'data'   => compact('path'),
        ]);
    }

    public function renameMedia(Request $request)
    {
        $data = $request->all();

        // TODO: check if the folder does not exists
        $validator = validator($data, [
            'media'    => 'required|array',
            'newName'  => 'required|string',
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
            ], 400);
        }

        // TODO Refactor this...
        $location = trim($data['location'], '/');
        $from     = $location.'/'.$data['media']['name'];

        switch ($data['media']['type']) {
            case Media::MEDIA_TYPE_FILE:
                $to = $this->moveFile($location, $from, $data);

                return response()->json([
                    'status' => 'success',
                    'data'   => ['path' => $to],
                ]);

            case Media::MEDIA_TYPE_DIRECTORY:
                $to = $this->moveDirectory($location, $from, $data);

                return response()->json([
                    'status' => 'success',
                    'data'   => ['path' => $to],
                ]);

            default:
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Something wrong was happened while renaming the media.',
                ], 400);
        }
    }

    public function deleteMedia(Request $request)
    {
        // TODO: Add validation
        $data = $request->all();
        $disk = $this->media->defaultDisk();

        // TODO Refactor this...
        if ($data['media']['type'] == Media::MEDIA_TYPE_FILE) {
            $deleted = $disk->delete($data['media']['path']);
        }
        else {
            $path = trim($data['media']['path'], '/');

            $deleted = $disk->deleteDirectory($path);
        }

        return response()->json(['status' => $deleted ? 'success' : 'error']);
    }

    public function moveLocations(Request $request)
    {
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

        return response()->json([
            'status' => 'success',
            'data'   => $destinations,
        ]);
    }

    public function moveMedia(Request $request)
    {
        $validator = validator($data = $request->all(), [
            'old-path' => 'required|string',
            'new-path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
            ], 400);
        }

        $moved = $this->media->move($data['old-path'], $data['new-path']);

        if ($moved) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        return $this->jsonResponseError('Something wrong happened !', 500);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */
    private function moveFile($location, $from, array $data)
    {
        $ext = pathinfo($data['media']['url'], PATHINFO_EXTENSION);
        $to  = $location.'/'.Str::slug(str_replace(".{$ext}", '', $data['newName'])).'.'.$ext;

        $this->media->move($from, $to);

        return $to;
    }

    private function moveDirectory($location, $from, array $data)
    {
        $to = $location.'/'.Str::slug($data['newName']);

        $this->media->move($from, $to);

        return $to;
    }
}
