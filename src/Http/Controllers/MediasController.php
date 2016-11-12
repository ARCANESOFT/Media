<?php namespace Arcanesoft\Media\Http\Controllers;

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
}
