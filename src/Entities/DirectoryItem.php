<?php namespace Arcanesoft\Media\Entities;

/**
 * Class     DirectoryItem
 *
 * @package  Arcanesoft\Media\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DirectoryItem extends MediaItem
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function type(): string
    {
        return static::TYPE_DIRECTORY;
    }

    protected function load(array $data, string $path)
    {
        // TODO: Implement load() method.
    }
}
