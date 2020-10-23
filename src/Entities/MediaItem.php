<?php namespace Arcanesoft\Media\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class     MediaItem
 *
 * @package  Arcanesoft\Media\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class MediaItem implements Arrayable, Jsonable, JsonSerializable
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    const TYPE_DIRECTORY = 'directory';
    const TYPE_FILE      = 'file';

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    public $path;

    public $name;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * MediaItem constructor.
     *
     * @param  array   $data
     * @param  string  $path
     */
    public function __construct(array $data, string $path)
    {
        $this->path = $path;
        $this->name = $data['name'];

        $this->load($data, $path);
    }

    abstract protected function load(array $data, string $path);

    /* -----------------------------------------------------------------
     |  Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get the item type.
     *
     * @return mixed
     */
    abstract public function type(): string;

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => $this->type(),
            'name' => $this->name,
            'path' => $this->path,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Get the instance as an array which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
