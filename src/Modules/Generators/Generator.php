<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\Project;
use Tapestry\Modules\Source\AbstractSource;
use Tapestry\Modules\Source\SourceInterface;

/**
 * Class Generator
 *
 * This is the base generator, if a source file is found to define generators in its
 * front matter its the job of this class to go over those generators and execute
 * them in the order in which they are defined.
 */
class Generator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * @var array|SourceInterface[]
     */
    private $generated = [];

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
        if (!$generators = $this->source->getData('generator')) {
            return [$this->source];
        }

        // To begin with, kick off the generation with the first generator. Then follow up with a loop that continues
        // until all generators have been ran.

        $first = reset($generators);
        $this->mergeGenerated($project->getContentGenerator($first, $this->source)->generate($project));

        // @todo there is scope for improvement with this loop, by removing items from $generated when they cant generate any more and placing them in a $result array for this method to return
        while ($this->canGenerate()) {
            foreach ($this->generated as $file) {
                if (! $generators = $file->getData('generator')) {
                    continue;
                }
                $first = reset($generators);
                $this->mergeGenerated($project->getContentGenerator($first, $file)->generate($project));
            }
        }

        return $this->generated;
    }

    /**
     * Identify whether we can continue generating.
     *
     * @return bool
     */
    private function canGenerate() : bool
    {
        foreach ($this->generated as $file) {
            if ($uses = $file->getData('generator')) {
                if (count($uses) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Merge the generated files into our local generatedFiles list.
     *
     * @param AbstractSource|AbstractSource[] $generated
     * @return void
     */
    private function mergeGenerated($generated)
    {
        if (! is_array($generated)) {
            $this->generated[$generated->getUid()] = $generated;
        } else {
            foreach ($generated as $file) {
                $this->mergeGenerated($file);
            }
        }
    }
}