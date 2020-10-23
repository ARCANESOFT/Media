<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Http\Requests;

use Arcanesoft\Media\Rules\{MediaItemExistsRule, MediaItemUniqueRule};
use Illuminate\Support\Str;

/**
 * Class     RenameMediaRequest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RenameMediaRequest extends FormRequest
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
        $manager  = static::getMediaManager();

        return [
            'old_path' => ['required', 'string', new MediaItemExistsRule($manager)],
            'new_path' => ['required', 'string', new MediaItemUniqueRule($manager)],
        ];
    }
}
