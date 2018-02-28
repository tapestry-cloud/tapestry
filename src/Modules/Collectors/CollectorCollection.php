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
     */
    public function add(CollectorInterface $class)
    {
        $this->items[] = $class;
    }

    public function collect()
    {
        foreach($this->items as $collector) {
            $collector->collect(); // @todo finish
        }
    }
}