<?php namespace Arcanesoft\Media\Entities;

use Illuminate\Support\Collection;

/**
 * Class     MediaCollection
 *
 * @package  Arcanesoft\Media\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MediaCollection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all the directories items.
     *
     * @param  array  $directories
     *
     * @return \Illuminate\Support\Collection
     */
    public static function directories(array $directories)
    {
        return Collection::make($directories)->mapInto(DirectoryItem::class);
    }

    /**
     * Get all the files items.
     *
     * @param  array  $files
     *
     * @return \Illuminate\Support\Collection
     */
    public static function files(array $files)
    {
        return Collection::make($files)->mapInto(FileItem::class);
    }

    /**
     * Get all the items.
     *
     * @param  array  $directories
     * @param  array  $files
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all(array $directories, array $files)
    {
        return Collection::make([
            static::directories($directories),
            static::files($files),
        ])->flatten(1);
    }
}
