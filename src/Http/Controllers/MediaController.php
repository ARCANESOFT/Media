<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Http\Controllers;

use Arcanesoft\Media\Policies\MediaPolicy;

/**
 * Class     MediaController
 *
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
        $this->authorize(MediaPolicy::ability('index'));

        $this->setCurrentSidebarItem('foundation::media');

        return $this->view('index');
    }
}
