<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\Project;
use Tapestry\Modules\Source\AbstractSource;

/**
 * Class ContentGeneratorFactory
 *
 * Content Generators are constructed on demand and injected with the
 * requesting source file. It's the role of this factory to create
 * new content generators as needed and register their
 * dependencies with the content graph.
 */
class ContentGeneratorFactory
{
    /**
     * Registered item stack.
     *
     * @var string[]
     */
    private $items = [];

    /**
     * @var Project
     */
    private $project;

    /**
     * ContentGeneratorCollection constructor.
     *
     * @param string[] $items
     * @param Project $project
     * @throws \ReflectionException
     */
    public function __construct(array $items = [], Project $project)
    {
        $this->project = $project;

        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Add a new FileGenerator definition to the Factory.
     *
     * @param string $class
     * @throws \ReflectionException
     */
    public function add(string $class)
    {
        $reflection = new \ReflectionClass($class);
        $this->items[$reflection->getShortName()] = $class;
    }

    /**
     * Return a new instance of the named FileGenerator constructed
     * with the AbstractSource.
     *
     * @param string $name
     * @param AbstractSource $file
     * @return GeneratorInterface
     */
    public function get(string $name, AbstractSource $file): GeneratorInterface
    {
        // @todo register new FileGenerator with the graph for #315
        return new $this->items[$name]($file);
    }

    /**
     * Return true if the factory has the named generator added.
     *
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        return isset($this->items[$name]);
    }
}
