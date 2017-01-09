<?php

namespace Tapestry\Entities;

use Tapestry\Tapestry;

class Cache
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $version;

    /**
     * Cache constructor.
     *
     * @param string $path
     */
    public function __construct($path, $hash)
    {
        clearstatcache();
        $this->path = $path;
        $this->hash = $hash;
        $this->version = Tapestry::VERSION;
    }

    public function load()
    {
        if (file_exists($this->path)) {
            $this->items = unserialize(file_get_contents($this->path));
        }
    }

    public function save()
    {
        file_put_contents($this->path, serialize($this->items));
    }

    public function setItem($key, $value)
    {
        $this->items[$key] = $value;
    }

    public function getItem($key)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
        return null;
    }

    public function count()
    {
        return count($this->items);
    }

    public function reset()
    {
        $this->items = [];
    }
}
