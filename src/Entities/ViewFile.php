<?php namespace Tapestry\Entities;

/**
 * Class ViewFile
 *
 * A wrapper surrounding a File object for the purpose of providing view helper
 * methods that are friendly to the end user.
 *
 * @package Tapestry\Entities
 */
class ViewFile
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var string
     */
    private $fileUid;

    /**
     * @var File
     */
    private $file;

    /**
     * ViewFile constructor.
     * @param Project $project
     * @param $fileUid
     */
    public function __construct(Project $project, $fileUid)
    {
        $this->project = $project;
        $this->fileUid = $fileUid;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        if (is_null($this->file)) {
            $this->file = $this->project->get('compiled.' . $this->fileUid);
        }

        return $this->file;
    }

    public function getData($key, $default = null)
    {
        return $this->getFile()->getData($key, $default);
    }

    public function getPermalink()
    {
        return $this->getFile()->getCompiledPermalink();
    }

    public function getUrl()
    {
        return url($this->getPermalink());
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->getData('date');
    }

    public function getContent()
    {
        if ($content = $this->getData('content')) {
            return $content;
        }

        return $this->getFile()->getContent();
    }

    public function isPaginated()
    {
        if (!$pagination = $this->getData('pagination')) {
            return false;
        }

        if (!$pagination instanceof Pagination) {
            return false;
        }

        return true;
    }

    public function hasPreviousNext()
    {
        if (!$previousNext = $this->getData('previous_next')) {
            return false;
        }

        if (!$previousNext instanceof \stdClass) {
            return false;
        }

        return true;
    }
}