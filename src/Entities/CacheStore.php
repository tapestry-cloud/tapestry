<?php

namespace Tapestry\Entities;

class CacheStore
{
    /**
     * Cached items.
     *
     * @var array
     */
    private $items = [];

    /**
     * Cache Store invalidation hash.
     *
     * @var string
     */
    private $hash;

    /**
     * The version of Tapestry this cache is bound to.
     *
     * @var string
     */
    private $version = null;

    /**
     * CacheStore constructor.
     *
     * @param string $hash
     * @param string $version
     */
    public function __construct($hash, $version)
    {
        $this->setTapestryVersion($version);
        $this->hash = $hash;
    }

    /**
     * Reset the cache store if $hash is different to the one in the store.
     *
     * @param $hash
     */
    public function validate($hash)
    {
        if ($hash !== $this->hash) {
            $this->reset();
        }
    }

    public function setTapestryVersion($version)
    {
        $this->version = $version;
    }

    public function getTapestryVersion()
    {
        return $this->version;
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
