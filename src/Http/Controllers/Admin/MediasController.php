<?php namespace Arcanesoft\Media\Http\Controllers\Admin;

use Arcanesoft\Media\Http\Controllers\Admin\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class     MediasController
 *
 * @package  Arcanesoft\Media\Http\Controllers\Admin
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediasController extends Controller
{
    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * MediasController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setCurrentPage('media');
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function index()
    {
        $this->setTitle('Media');

        return $this->view('manager');
    }

    public function getAll(Request $request)
    {
        $location = $request->get('location');
        $location = trim($location, '/');

        return response()->json([
            'status' => 'success',
            'data'   => array_merge(
                $this->getDirectoriesFromLocation($location),
                $this->getFilesFromLocation($location)
            ),
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'location' => 'required',
            'medias'   => 'required|array',
            'medias.*' => 'required|file'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->messages()]);
        }

        $location = $request->get('location');

        foreach ($request->file('medias') as $media) {
            /** @var \Illuminate\Http\UploadedFile  $media */
            $media->store($location, $this->getDefaultDiskDriver());
        }

        return response()->json(['status' => 'success']);
    }

    public function createDirectory(Request $request)
    {
        $data      = $request->all();
        $validator = \Validator::make($data, [
            'name'     => 'required', // TODO: check if the folder does not exists
            'location' => 'required',
        ]);

        $path = trim($data['location'], '/') . '/' . str_slug($data['name']);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
            ], 400);
        }

        $this->disk()->makeDirectory($path);

        return response()->json([
            'status' => 'success',
            'data'   => compact('path'),
        ]);
    }

    public function renameMedia(Request $request)
    {
        $data      = $request->all();

        // TODO: check if the folder does not exists
        $validator = \Validator::make($data, [
            'media'    => 'required',
            'newName'  => 'required',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
            ], 400);
        }

        $location = trim($data['location'], '/');
        $media    = $data['media'];
        $src  = $location . '/' . $media['name'];

        if ($media['type'] == 'file') {
            $ext = pathinfo($media['url'], PATHINFO_EXTENSION);
            $dest = $location . '/' . Str::slug(str_replace(".{$ext}", '', $data['newName'])).'.'.$ext;
            $this->disk()->move($src, $dest);

            return response()->json([
                'status' => 'success',
                'data'   => ['path' => $dest],
            ]);
        }
        elseif ($media['type'] == 'directory') {
            $dest = $location . '/' . Str::slug($data['newName']);
            $this->disk()->move($src, $dest);

            return response()->json([
                'status' => 'success',
                'data'   => ['path' => $dest],
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Something wrong was happened while renaming the media.',
        ], 400);
    }

    public function deleteMedia(Request $request)
    {
        // TODO: Add validation
        $data = $request->all();

        if ($data['media']['type'] == 'file') {
            $deleted = $this->disk()->delete($data['media']['path']);
        }
        else {
            $path = trim($data['media']['path'], '/');

            $deleted = $this->disk()->deleteDirectory($path);
        }

        return response()->json(['status' => $deleted ? 'success' : 'error']);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the default disk driver.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    private function getDefaultDiskDriver()
    {
        return config('arcanesoft.media.filesystem.default');
    }

    /**
     * Get the disk adapter.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    private function disk()
    {
        return Storage::disk($this->getDefaultDiskDriver());
    }

    /**
     * @param  string  $location
     *
     * @return array
     */
    private function getDirectoriesFromLocation($location)
    {
        return array_map(function ($directory) use ($location) {
            return [
                'name' => str_replace("$location/", '', $directory),
                'path' => $directory,
                'type' => 'directory',
            ];
        }, $this->disk()->directories($location));
    }

    /**
     * @param  string  $location
     *
     * @return array
     */
    private function getFilesFromLocation($location)
    {
        $disk   = $this->disk();

        return array_map(function ($path) use ($disk, $location) {
            return [
                'name'         => str_replace("$location/", '', $path),
                'type'         => 'file',
                'path'         => $path,
                'url'          => $disk->url($path),
                'mimetype'     => $disk->mimeType($path),
                'lastModified' => Carbon::createFromTimestamp($disk->lastModified($path))->toDateTimeString(),
                'visibility'   => $disk->getVisibility($path),
                'size'         => $disk->size($path),
            ];
        }, $disk->files($location));
    }
}
