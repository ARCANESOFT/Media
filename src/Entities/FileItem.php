<?php namespace Arcanesoft\Media\Entities;

/**
 * Class     FileItem
 *
 * @package  Arcanesoft\Media\Entities
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

    protected function load(array $data, string $path)
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
     * Get the item type.
     *
     * @return mixed
     */
    public function type(): string
    {
        return static::TYPE_FILE;
    }

    /**
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
