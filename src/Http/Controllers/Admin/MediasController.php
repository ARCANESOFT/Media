<?php namespace Arcanesoft\Media\Http\Controllers\Admin;

use Arcanesoft\Media\Policies\MediasPolicy;

/**
 * Class     MediasController
 *
 * @package  Arcanesoft\Media\Http\Controllers\Admin
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediasController extends Controller
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * MediasController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setCurrentPage('media');
        $this->addBreadcrumbRoute('Media', 'admin::media.index');
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
}
