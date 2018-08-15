<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\Project;
use Tapestry\Modules\Source\SourceInterface;

/**
 * Interface GeneratorInterface
 *
 * Each Generator should implement this basic interface and
 * regardless of whether they generate one file or many
 * the generate method should always return an array.
 */
interface GeneratorInterface
{
    /**
     * Set the source that this generator works from.
     *
     * @param SourceInterface $source
     * @return void
     */
    public function setSource(SourceInterface $source);

    /**
     * Run the generation and return an array of generated
     * files (oddly implementing SourceInterface, naming
     * things is hard!)
     *
     * @param Project $project
     * @return array|SourceInterface[]
     */
    public function generate(Project $project): array;
}