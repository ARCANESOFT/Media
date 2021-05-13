<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Http\Requests;

use Arcanesoft\Media\Rules\MediaItemExistsRule;

/**
 * Class     UploadMediaRequest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UploadMediaRequest extends FormRequest
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $manager = static::getMediaManager();

        return [
            'location' => ['required', 'string', new MediaItemExistsRule($manager)],
            'files.*'  => ['file'],
        ];
    }
}
