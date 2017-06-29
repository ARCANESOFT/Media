<?php namespace Arcanesoft\Media\Entities;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class     DirectoryCollection
 *
 * @package  Arcanesoft\Media\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DirectoryCollection extends Collection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * ExcludePattern directories with the given patterns.
     *
     * @param  array  $patterns
     *
     * @return self
     */
    public function exclude(array $patterns)
    {
        return $this->reject(function ($directory) use ($patterns) {
            foreach ($patterns as $pattern)
                if (Str::is($pattern, $directory['path'])) return true;

            return false;
        });
    }
}
