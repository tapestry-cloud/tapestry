<?php

namespace Tapestry\Entities;

class CacheStore
{
    private $items = [];

    private $version;

    private $hash;

    public function __construct($hash, $version)
    {
        $this->hash = $hash;
        $this->version = $version;
    }

    public function validate($hash, $version)
    {
        if ($hash !== $this->hash || $version !== $this->version) {
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

    public function reset()
    {
        $this->items = [];
    }
}