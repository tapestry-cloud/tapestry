<?php namespace Tapestry\Entities\Generators;

use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFileGeneratorInterface;
use Tapestry\Entities\ProjectFileInterface;

class FileGenerator implements ProjectFileInterface, ProjectFileGeneratorInterface
{
    /**
     * @var File
     */
    protected $file;

    /**
     * FileGenerator constructor.
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @param Project $project
     * @return ProjectFileInterface|ProjectFileInterface[]
     */
    public function generate(Project $project)
    {
        if ($generators = $this->file->getData('generator')) {
            $first = reset($generators);
            return $project->getContentGenerator($first, $this->file)->generate($project);
        }else{
            return $this->file;
        }
    }

    public function __call($name, $arguments)
    {
        if (! method_exists($this, $name) && method_exists($this->file, $name)) {
            return call_user_func_array([$this->file, $name], $arguments);
        }
    }
}