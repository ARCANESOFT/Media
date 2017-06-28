<?php namespace Arcanesoft\Media\Helpers;

use Illuminate\Support\Str;

/**
 * Class     ExcludePattern
 *
 * @package  Arcanesoft\Media\Helpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ExcludePattern
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * @param  array  $patterns
     *
     * @return array
     */
    public static function directories(array $patterns)
    {
        $patterns = array_map(function ($pattern) {
            return trim($pattern, '/');
        }, $patterns);

        foreach ($patterns as $pattern) {
            $patterns[] = Str::endsWith($pattern, '/*')
                ? Str::replaceLast('/*', '', $pattern)
                : $pattern.'/*';
        }

        asort($patterns);

        return array_values(array_unique($patterns));
    }
}
