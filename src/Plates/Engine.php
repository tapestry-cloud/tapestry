<?php

namespace Tapestry\Plates;

use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFile;
use League\Plates\Engine as LeagueEngine;
use Tapestry\Entities\Collections\FlatCollection;

/**
 * Class Engine.
 * @deprecated
 */
class Engine extends LeagueEngine
{
    /**
     * @var Project
     */
    private $project;

    public function setProject(Project $project)
    {
        $this->project = $project;
        $this->project->set('file_layout_cache', new FlatCollection());
    }

    public function getProject()
    {
        return $this->project;
    }

    /**
     * Create a new template.
     *
     * @param string $name
     *
     * @return Template
     */
    public function make($name)
    {
        return new Template($this, $name);
    }

    /**
     * Create a new template and render it.
     *
     * @param ProjectFile   $file
     *
     * @return string
     */
    public function renderFile(ProjectFile $file)
    {
        return $this->make(
            $file->getFileInfo()->getRelativePath().
            DIRECTORY_SEPARATOR.
            pathinfo($file->getFileInfo()->getFilename(), PATHINFO_FILENAME)
        )->renderFile($file);
    }
}
