<?php

namespace Tapestry\Modules\Collectors;

class CollectorCollection
{
    /**
     * @var array|CollectorInterface[]
     */
    private $items = [];

    /**
     * @param CollectorInterface $class
     * @throws \ReflectionException
     */
    public function add(CollectorInterface $class)
    {
        $reflection = new \ReflectionClass($class);
        $this->items[$reflection->getShortName()] = $class;
    }

    public function collect()
    {
        foreach($this->items as $collector) {
            $collector->collect(); // @todo finish
        }
    }
}