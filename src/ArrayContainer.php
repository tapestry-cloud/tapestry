<?php

namespace Tapestry;

use ArrayAccess;
use Closure;
use Iterator;

class ArrayContainer implements ArrayAccess, Iterator
{
    /**
     * Current array key pointer for foreach.
     *
     * @var int
     */
    private $index = 0;

    /**
     * Data item array.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Nested Key Cache.
     *
     * @var array
     */
    protected $nestedKeyCache = [];

    /**
     * ArrayContainer constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Push value onto the collection.
     *
     * @deprecated this isn't used, it also doesn't work in a multi dimensional way with dot notation. Do Not Use.
     *
     * @param mixed $value
     */
    public function push($value)
    {
        array_push($this->items, $value);
        $this->nestedKeyCache = [];
    }

    /**
     * Add or amend an item in the container by $key.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        if ($this->isNestedKey($key)) {
            $this->setNestedValueByKey($key, $value);
        } else {
            $this->items[$key] = $value;
        }
        $this->nestedKeyCache = [];
    }

    /**
     * Remove an items from the container by $key.
     *
     * @param string $key
     */
    public function remove($key)
    {
        if ($this->isNestedKey($key)) {
            $this->removeNestedValueByKey($key);
        } else {
            unset($this->items[$key]);
        }

        $this->removeKeyFromNestedCache($key);
    }

    /**
     * Get an item from the container by $key if it exists or else return $default.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        if (!$this->isNestedKey($key)) {
            return $this->items[$key];
        } else {
            return $this->getNestedValueByKey($key);
        }
    }

    /**
     * Returns boolean true if the $key exists in this container.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        if (!$this->isNestedKey($key)) {
            return isset($this->items[$key]);
        } else {
            return !is_null($this->getNestedValueByKey($key));
        }
    }

    /**
     * @toto implement feature
     *
     * @param array $items
     */
    public function merge(array $items)
    {
        $this->items = $this->arrayMergeRecursive($this->items, $items);
        $this->nestedKeyCache = [];
    }

    /**
     * Return all items within the container.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    public function count()
    {
        return count($this->items);
    }

    /**
     * Recursive array merge found from stackoverflow.
     *
     * @link http://stackoverflow.com/a/25712428/1225977
     *
     * @param array $left
     * @param array $right
     *
     * @return array
     */
    private function arrayMergeRecursive(array &$left, array &$right)
    {
        $merged = $left;
        foreach ($right as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMergeRecursive($merged[$key], $value);
            } else {
                if (is_numeric($key)) {
                    if (!in_array($value, $merged)) {
                        $merged[] = $value;
                    }
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    private function isNestedKey($key)
    {
        return str_contains($key, '.');
    }

    private function removeNestedValueByKey($key)
    {
        // Bust Cache
        $this->removeKeyFromNestedCache($key);

        // Check to see if this is targeting an instance of ArrayContainer and pass the nested value on
        $keyParts = explode('.', $key);
        if ($this->get($keyParts[0]) instanceof self && $arrayContainer = $this->get($keyParts[0])) {
            array_shift($keyParts);
            /* @var ArrayContainer $arrayContainer */
            $arrayContainer->remove(implode('.', $keyParts));

            return true;
        }
        unset($keyParts);

        $items = &$this->items;
        $keyParts = explode('.', $key);
        $lastKeyPart = end($keyParts);
        foreach ($keyParts as $keyPart) {
            if ($keyPart === $lastKeyPart) {
                unset($items[$keyPart]);
                break;
            }
            $items = &$items[$keyPart];
        }

        return true;
    }

    private function setNestedValueByKey($key, $value)
    {

        // Bust Cache
        $this->removeKeyFromNestedCache($key);

        // Check to see if this is targeting an instance of ArrayContainer and pass the nested value on
        $keyParts = explode('.', $key);
        if ($this->get($keyParts[0]) instanceof self && $arrayContainer = $this->get($keyParts[0])) {
            array_shift($keyParts);
            /* @var ArrayContainer $arrayContainer */
            $arrayContainer->set(implode('.', $keyParts), $value);

            return true;
        }
        unset($keyParts);

        $items = &$this->items;
        foreach (explode('.', $key) as $keyPart) {
            $items = &$items[$keyPart];
        }

        return $items = $value;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    private function getNestedValueByKey($key)
    {
        if (isset($this->nestedKeyCache[$key])) {
            return $this->nestedKeyCache[$key];
        }

        // Check to see if this is targeting an instance of ArrayContainer and grab it from the nested container
        $keyParts = explode('.', $key);
        if ($this->get($keyParts[0]) instanceof self && $arrayContainer = $this->get($keyParts[0])) {
            array_shift($keyParts);
            /* @var ArrayContainer $arrayContainer */
            $value = $arrayContainer->get(implode('.', $keyParts));
        } else {
            $value = $this->items;
            foreach (explode('.', $key) as $keyPart) {
                if ((!is_array($value) || !$value instanceof self)) {
                    if (is_object($value) && method_exists($value, 'arrayAccessByKey')) {
                        if ($value = $value->arrayAccessByKey($keyPart)) {
                            break;
                        } else {
                            return;
                        }
                    }
                }

                if (!isset($value[$keyPart])) {
                    return;
                }

                if (is_array($value[$keyPart]) && !array_key_exists($keyPart, $value)) {
                    return;
                }
                $value = $value[$keyPart];
            }
        }

        $this->nestedKeyCache[$key] = $value;

        return $value;
    }

    private function removeKeyFromNestedCache($key)
    {
        if (isset($this->nestedKeyCache[$key])) {
            unset($this->nestedKeyCache[$key]);
        } else {
            $this->nestedKeyCache = array_filter($this->nestedKeyCache, function ($arrayKey) use ($key) {
                $n = strpos($arrayKey, $key) === false;

                return $n;
            }, ARRAY_FILTER_USE_KEY);
        }
    }

    /**
     * Whether a offset exists.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset
     *
     * @return bool true on success or false on failure. The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Return the current element.
     *
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return mixed Can return any type.
     *
     * @since 5.0.0
     */
    public function current()
    {
        $k = array_keys($this->items);
        $var = $this->items[$k[$this->index]];

        return $var;
    }

    /**
     * Move forward to next element.
     *
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void Any returned value is ignored.
     *
     * @since 5.0.0
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Return the key of the current element.
     *
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure.
     *
     * @since 5.0.0
     */
    public function key()
    {
        $k = array_keys($this->items);
        $var = $k[$this->index];

        return $var;
    }

    /**
     * Checks if current position is valid.
     *
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     *              Returns true on success or false on failure.
     *
     * @since 5.0.0
     */
    public function valid()
    {
        $k = array_keys($this->items);

        return isset($k[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     *
     * @return void Any returned value is ignored.
     *
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($value) {
            return $value instanceof ArrayContainer ? $value->toArray() : $value;
        }, $this->items);
    }

    /**
     * Output the container as an array.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Sort through each item within the container by callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function sort(Closure $callback)
    {
        uasort($this->items, $callback);

        return $this;
    }

    /**
     * A 2D array sort, useful for when you need to sort a two dimensional array.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function sortMultiDimension(Closure $callback)
    {
        foreach ($this->items as &$sortable) {
            uasort($sortable, $callback);
        }
        unset($sortable);

        return $this;
    }

    /**
     * Allow the filtering of items by key.
     *
     * @param array $filteredKeys
     */
    public function filterKeys(array $filteredKeys = [])
    {
        $this->items = array_filter($this->items, function ($key) use ($filteredKeys) {
            return !isset($filteredKeys[$key]);
        }, ARRAY_FILTER_USE_KEY);
    }
}
