<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Http\Requests;

use Arcanesoft\Media\MediaManager;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

/**
 * Class     FormRequest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class FormRequest extends BaseFormRequest
{
    /* -----------------------------------------------------------------
     |  Common Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the media manager instance.
     *
     * @return \Arcanesoft\Media\MediaManager
     */
    protected static function getMediaManager()
    {
        return app(MediaManager::class);
    }
}
