<?php namespace Tapestry\Entities;

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
     * Cache constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        clearstatcache();
        $this->path = $path;
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
        if (isset($this->items[$key])){
            return $this->items[$key];
        }

        return null;
    }

    public function reset()
    {
        $this->items = [];
    }

}