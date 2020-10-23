<?php namespace Arcanesoft\Media\Http\Controllers;

/**
 * Class     MediaController
 *
 * @package  Arcanesoft\Media\Http\Controllers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaController extends Controller
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function index()
    {
        // TODO: Add authorization check
        $this->setCurrentSidebarItem('foundation::media');

        return $this->view('index');
    }
}
