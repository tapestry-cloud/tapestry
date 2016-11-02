<?php namespace Tapestry\Entities;

use Iterator;
use Tapestry\ArrayContainer;

// do we need search/filter/order/pagination/etc functionality baked into this class?
class Collection extends ArrayContainer implements Iterator {

    /**
     * Current array key pointer for foreach
     * @var int
     */
    private $index = 0;

    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $k = array_keys($this->items);
        $var = $this->items[$k[$this->index]];
        return $var;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        $k = array_keys($this->items);
        $var = $k[$this->index];
        return $var;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        $k = array_keys($this->items);
        return isset($k[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->index = 0;
    }
}