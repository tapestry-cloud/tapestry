<?php

namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFile;
use Tapestry\Entities\ProjectFileInterface;
use Tapestry\Entities\ProjectFileGeneratorInterface;

/**
 * Class FileGenerator
 * @deprecated
 */
class FileGenerator implements ProjectFileInterface, ProjectFileGeneratorInterface
{
    /**
     * @var ProjectFile
     */
    protected $file;

    /**
     * @var array|ProjectFile[]
     */
    private $generatedFiles = [];

    /**
     * FileGenerator constructor.
     *
     * @param ProjectFile $file
     */
    public function __construct(ProjectFile $file)
    {
        $this->file = $file;
    }

    /**
     * @param Project $project
     *
     * @return ProjectFileInterface|ProjectFileInterface[]
     */
    public function generate(Project $project)
    {
        if ($generators = $this->file->getData('generator')) {
            // Kick off the generation with the first generator. Because ProjectFile generators can either mutate the current ProjectFile
            // or generate an array of ProjectFile's we then continue the generation with a while loop until all generators have been
            // run resulting in a flat array.
            $first = reset($generators);
            $this->mergeGenerated($project->getContentGenerator($first, $this->file)->generate($project));

            while ($this->canGenerate()) {
                foreach ($this->generatedFiles as $file) {
                    if (! $generators = $file->getData('generator')) {
                        continue;
                    }
                    $first = reset($generators);
                    $this->mergeGenerated($project->getContentGenerator($first, $file)->generate($project));
                }
            }

            return $this->generatedFiles;
        } else {
            return $this->file;
        }
    }

    public function __call($name, $arguments)
    {
        if (! method_exists($this, $name) && method_exists($this->file, $name)) {
            return call_user_func_array([$this->file, $name], $arguments);
        }
    }

    /**
     * Identify whether we can continue generating.
     *
     * @return bool
     */
    private function canGenerate() : bool
    {
        foreach ($this->generatedFiles as $file) {
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
     * @param ProjectFile|ProjectFile[] $generated
     * @return void
     */
    private function mergeGenerated($generated)
    {
        if (! is_array($generated)) {
            $this->generatedFiles[$generated->getUid()] = $generated;
        } else {
            foreach ($generated as $file) {
                $this->mergeGenerated($file);
            }
        }
    }
}
