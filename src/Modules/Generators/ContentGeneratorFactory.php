<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\ProjectFile;

class ContentGeneratorFactory
{
    /**
     * @var array
     */
    private $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add($class)
    {
        $reflection = new \ReflectionClass($class);
        $this->items[$reflection->getShortName()] = $class;
    }

    public function get($name, ProjectFile $file)
    {
        return new $this->items[$name]($file);
    }

    public function has($name)
    {
        return isset($this->items[$name]);
    }
}
