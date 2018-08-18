<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\DependencyGraph\Node;
use Tapestry\Entities\Project;
use Tapestry\Modules\Source\SourceInterface;

abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var SourceInterface|Node
     */
    protected $source;

    /**
     * Set the source that this generator works from.
     *
     * @param SourceInterface $source
     * @return void
     */
    public function setSource(SourceInterface $source)
    {
        $this->source = $source;
    }

    /**
     * Run the generation and return an array of generated
     * files (oddly implementing SourceInterface, naming
     * things is hard!)
     *
     * @param Project $project
     * @return array|SourceInterface[]
     */
    abstract public function generate(Project $project): array;
}