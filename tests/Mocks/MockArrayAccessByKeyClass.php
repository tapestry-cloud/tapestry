<?php

namespace Tapestry\Tests\Mocks;

class MockArrayAccessByKeyClass
{
    private $items = [];

    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public function arrayAccessByKey($key)
    {
        if (!isset($this->items[$key])) {
            return;
        }

        return $this->items[$key];
    }
}
