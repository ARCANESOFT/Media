<?php namespace Arcanesoft\Media\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class     MediasController
 *
 * @package  Arcanesoft\Media\Http\Controllers
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
        dd($request->all());
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
        $path = trim($data['media']['path'], '/');

        $this->disk()->deleteDirectory($path);

        return response()->json([
            'status' => 'success',
        ]);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the disk adapter.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    protected function disk()
    {
        return Storage::disk(config('arcanesoft.media.filesystem.default'));
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

    private function isLocalDisk()
    {
        $driver = config('arcanesoft.media.filesystem.default');

        return config("arcanesoft.media.filesystem.disks.{$driver}.driver", 'local');
    }
}
