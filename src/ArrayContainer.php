<?php namespace Tapestry;

use Closure;

class ArrayContainer implements \ArrayAccess
{
    /**
     * Data item array
     * @var array
     */
    protected $items = [];

    /**
     * Nested Key Cache
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
     * Add or amend an item in the container by $key
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        if ($this->isNestedKey($key)) {
            $this->setNestedValueByKey($key, $value);
        }else{
            $this->items[$key] = $value;
        }
        $this->nestedKeyCache = [];
    }

    /**
     * Remove an items from the container by $key
     *
     * @param string $key
     */
    public function remove($key)
    {
        unset($this->items[$key]);
        $this->nestedKeyCache = [];
    }

    /**
     * Get an item from the container by $key if it exists or else return $default
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        if (!$this->isNestedKey($key)) {
            return $this->items[$key];
        }else{
            return $this->getNestedValueByKey($key);
        }
    }

    /**
     * Returns boolean true if the $key exists in this container
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        if (!$this->isNestedKey($key)) {
            return isset($this->items[$key]);
        }else{
            return !is_null($this->getNestedValueByKey($key));
        }
    }

    /**
     * @toto implement feature
     * @param array $items
     */
    public function merge(array $items)
    {
        $this->items = $this->arrayMergeRecursive($this->items, $items);
        $this->nestedKeyCache = [];
    }

    /**
     * Return all items within the container
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
     * Recursive array merge found from stackoverflow
     *
     * @link http://stackoverflow.com/a/25712428/1225977
     * @param array $left
     * @param array $right
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
     * @return bool
     */
    private function isNestedKey($key) {
        return str_contains($key, '.');
    }

    private function setNestedValueByKey($key, $value) {
        $items = &$this->items;
        foreach (explode('.', $key) as $keyPart) {
            $items = &$items[$keyPart];
        }

        return $items = $value;

    }

    /**
     * @param string $key
     * @return string|null
     */
    private function getNestedValueByKey($key) {
        if (isset($this->nestedKeyCache[$key])) {
            return $this->nestedKeyCache[$key];
        }

        $value = $this->items;
        foreach (explode('.', $key) as $keyPart) {
            if (! isset($value[$keyPart])) {
                return null;
            }

            if ( ! is_array($value[$keyPart]) && ! array_key_exists($keyPart, $value) ) {
                return null;
            }
            $value = $value[$keyPart];
        }

        $this->nestedKeyCache[$key] = $value;
        return $value;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * @return boolean true on success or false on failure. The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
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
     * Output the container as an array
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Sort through each item within the container by callback
     *
     * @param Closure $callback
     * @return $this
     */
    public function sort(Closure $callback)
    {
        uasort($this->items, $callback);
        return $this;
    }
}