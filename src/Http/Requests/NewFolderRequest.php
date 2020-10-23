<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Http\Requests;

use Arcanesoft\Media\Rules\MediaItemExistsRule;
use Arcanesoft\Media\Rules\MediaItemUniqueRule;
use Illuminate\Support\Str;

/**
 * Class     NewFolderRequest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class NewFolderRequest extends FormRequest
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
            'path' => ['required', 'string', new MediaItemUniqueRule($manager)],
        ];
    }
}
