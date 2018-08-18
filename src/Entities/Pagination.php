<?php

namespace Tapestry\Entities;

/**
 * Class Pagination
 * @todo refactor, see todo below
 */
class Pagination
{
    /**
     * This pages items.
     *
     * @var array
     */
    private $items;

    /**
     * Total pages in pagination.
     *
     * @var int
     */
    public $totalPages;

    /**
     * Current page in pagination.
     *
     * @var int
     */
    public $currentPage;

    /**
     * @var Project
     */
    private $project;

    /**
     * Is the local cache hot?
     *
     * @var bool
     */
    private $loaded = false;

    /**
     * @var null|string
     */
    private $next;

    /**
     * @var null|string
     */
    private $previous;

    /**
     * Pages in pagination group.
     *
     * @var array|ViewFile[]
     */
    private $pages = [];

    /**
     * Pagination constructor.
     *
     * // @todo why is this passed in Project and an array of item keys. It should pass a list of the Sources!
     *
     * @param Project $project
     * @param array   $items
     * @param int     $totalPages
     * @param int     $currentPage
     */
    public function __construct(Project $project, array $items = [], $totalPages = 0, $currentPage = 0)
    {
        $this->items = $items;
        $this->totalPages = $totalPages;
        $this->currentPage = $currentPage;
        $this->project = $project;
    }

    public function getItems()
    {
        if ($this->loaded === true) { // @todo loaded is never set
            return $this->items;
        }

        array_walk_recursive($this->items, function (&$file, $fileKey) {
            /** @var ProjectFile $compiledFile */
            if (! $compiledFile = $this->project->get('compiled.'.$fileKey)) {
                $file = null;
            } else {
                $file = new ViewFile($this->project, $compiledFile->getUid());
            }
        });

        return $this->items;
    }

    /**
     * @param string $previous
     * @param string $next
     */
    public function setPreviousNext($previous, $next)
    {
        $this->previous = $previous;
        $this->next = $next;
    }

    /**
     * Get the next page, or null if we are the last page.
     *
     * @return null|ViewFile
     */
    public function getNext()
    {
        return is_null($this->next) ? null : new ViewFile($this->project, $this->next);
    }

    /**
     * Get the previous page, or null if we are the first page.
     *
     * @return null|ViewFile
     */
    public function getPrevious()
    {
        return is_null($this->previous) ? null : new ViewFile($this->project, $this->previous);
    }

    /**
     * Is this the first page in the pagination?
     *
     * @return bool
     */
    public function isFirst()
    {
        return is_null($this->previous);
    }

    /**
     * Is this the last page in the pagination?
     *
     * @return bool
     */
    public function isLast()
    {
        return is_null($this->next);
    }

    /**
     * @param array|ProjectFile[] $pages
     */
    public function setPages(array $pages = [])
    {
        foreach ($pages as $file) {
            array_push($this->pages, new ViewFile($this->project, $file->getUid()));
        }
    }

    /**
     * @return array|ViewFile[]
     */
    public function getPages()
    {
        return $this->pages;
    }
}
