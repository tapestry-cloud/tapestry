<?php namespace Tapestry\Entities;

use Iterator;
use Tapestry\ArrayContainer;

// do we need search/filter/order/pagination/etc functionality baked into this class?
class FlatCollection extends ArrayContainer {
    public function set($key, $value)
    {
        $this->items[$key] = $value;
    }

    public function has($key)
    {
        return isset($this->items[$key]);
    }

    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }
        return $this->items[$key];
    }
}