<?php

namespace Tapestry\Entities;
use Tapestry\Modules\Source\AbstractSource;

/**
 * Class ViewFile.
 *
 * This is a wrapper around the parsed AbstractSource intended for
 * consumption in the templates files and therefore provides
 * the helper methods available in ViewFileTrait.
 */
class ViewFile
{
    use ViewFileTrait;

    /**
     * @var Project
     * @deprecated
     */
    private $project;

    /**
     * @var string
     */
    private $fileUid;

    /**
     * @var AbstractSource
     */
    private $file;

    /**
     * ViewFile constructor.
     *
     * @todo remove dependence on Project and only pass in AbstractSource
     * @param Project $project
     * @param $fileUid
     */
    public function __construct(Project $project, $fileUid)
    {
        $this->project = $project;
        $this->fileUid = $fileUid;
    }

    /**
     * @return AbstractSource
     */
    public function getSource(): AbstractSource
    {
        if (is_null($this->file)) {
            $this->file = $this->project->get('compiled.'.$this->fileUid);
        }

        return $this->file;
    }
}
