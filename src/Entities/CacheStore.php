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
     * CacheStore constructor.
     *
     * @param $hash
     */
    public function __construct($hash)
    {
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
