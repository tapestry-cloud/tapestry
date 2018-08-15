<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\Project;
use Tapestry\Modules\Source\SourceInterface;

class CollectionItemGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * Run the generation and return an array of generated
     * files (oddly implementing SourceInterface, naming
     * things is hard!)
     *
     * @param Project $project
     * @return array|SourceInterface[]
     */
    public function generate(Project $project): array
    {
        // TODO: Implement generate() method.
    }
}