<?php

namespace Tapestry\Entities;

/**
 * Class ViewFile.
 *
 * A wrapper surrounding a ProjectFile object for the purpose of providing view helper
 * methods that are friendly to the end user.
 *
 * // @todo -> ViewSource?
 */
class ViewFile
{
    use ViewFileTrait;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var string
     */
    private $fileUid;

    /**
     * @var ProjectFile
     */
    private $file;

    /**
     * ViewFile constructor.
     *
     * @param Project $project
     * @param $fileUid
     */
    public function __construct(Project $project, $fileUid)
    {
        $this->project = $project;
        $this->fileUid = $fileUid;
    }

    /**
     * @return ProjectFile
     */
    public function getFile()
    {
        if (is_null($this->file)) {
            $this->file = $this->project->get('compiled.'.$this->fileUid);
        }

        return $this->file;
    }
}
