<?php

declare(strict_types=1);

namespace Arcanesoft\Media\Entities;

/**
 * Class     FileItem
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FileItem extends MediaItem
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    public $url;

    public $mimetype;

    public $lastModified;

    public $visibility;

    public $size;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Load the media item.
     *
     * @param  array   $data
     * @param  string  $path
     */
    protected function load(array $data, string $path): void
    {
        $this->url          = $data['url'];
        $this->mimetype     = $data['mimetype'];
        $this->lastModified = $data['lastModified'];
        $this->visibility   = $data['visibility'];
        $this->size         = $data['size'];
    }

    /* -----------------------------------------------------------------
     |  Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get the media item's type.
     *
     * @return string
     */
    public function type(): string
    {
        return static::TYPE_FILE;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'url'          => $this->url,
            'mimetype'     => $this->mimetype,
            'lastModified' => $this->lastModified,
            'visibility'   => $this->visibility,
            'size'         => $this->size,
        ]);
    }
}
