<?php namespace Tapestry\Tests;

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
            return null;
        }
        return $this->items[$key];
    }
}