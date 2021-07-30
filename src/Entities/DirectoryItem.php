<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Entities;

/**
 * Class     DirectoryItem
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DirectoryItem extends MediaItem
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the media item's type.
     *
     * @return string
     */
    public function type(): string
    {
        return static::TYPE_DIRECTORY;
    }

    /**
     * Load the media item.
     *
     * @param  array   $data
     * @param  string  $path
     */
    protected function load(array $data, string $path): void
    {
        // TODO: Implement load() method.
    }
}
