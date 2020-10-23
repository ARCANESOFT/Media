<?php

namespace Arcanesoft\Media\Http\Controllers;

use Arcanesoft\Foundation\Support\Http\Controller as BaseController;
use Arcanesoft\Media\MediaManager;

/**
 * Class     Controller
 *
 * @package  Arcanesoft\Media\Http\Controllers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class Controller extends BaseController
{
    /* -----------------------------------------------------------------
    |  Properties
    | -----------------------------------------------------------------
    */

    /** @var  \Arcanesoft\Media\MediaManager */
    protected $manager;

    /**
     * The view namespace.
     *
     * @var string|null
     */
    protected $viewNamespace = 'media';

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    public function __construct(MediaManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;

        $this->addBreadcrumbRoute(__('Media'), 'admin::media.index');
    }
}
