<?php namespace Arcanesoft\Media\Entities;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class     FileCollection
 *
 * @package  Arcanesoft\Media\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileCollection extends Collection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * ExcludePattern files with the given patterns.
     *
     * @param  array  $patterns
     *
     * @return self
     */
    public function exclude(array $patterns)
    {
        return $this->reject(function ($file) use ($patterns) {
            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $file['name'])) return true;
            }

            return false;
        });
    }
}
