<?php

namespace Tapestry\Entities;

class Cache
{
    /**
     * @var CacheStore
     */
    private $store;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $hash;

    /**
     * Cache constructor.
     *
     * @param string $path
     * @param string $hash
     */
    public function __construct($path, $hash)
    {
        clearstatcache();
        $this->path = $path;
        $this->hash = $hash;
        $this->store = new CacheStore($this->hash);
    }

    public function load()
    {
        if (file_exists($this->path)) {
            $this->store = unserialize(file_get_contents($this->path));
            $this->store->validate($this->hash);
        }
    }

    public function save()
    {
        file_put_contents($this->path, serialize($this->store));
    }

    public function setItem($key, $value)
    {
        $this->store->setItem($key, $value);
    }

    public function getItem($key)
    {
        return $this->store->getItem($key);
    }

    public function count()
    {
        return $this->store->count();
    }

    public function reset()
    {
        $this->store->reset();
    }
}
