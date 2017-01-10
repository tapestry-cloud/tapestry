<?php

namespace Tapestry\Entities;

class CacheStore
{
    private $items = [];

    private $hash;

    public function __construct($hash)
    {
        $this->hash = $hash;
    }

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
