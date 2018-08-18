<?php

namespace Tapestry\Entities;

use Tapestry\Modules\Source\SourceInterface;

/**
 * Class Pagination
 * @todo refactor, see todo below
 */
class Pagination
{
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
     * @var null|SourceInterface
     */
    private $next;

    /**
     * @var null|SourceInterface
     */
    private $previous;

    /**
     * Pages in pagination group.
     *
     * @var array|SourceInterface[]
     */
    private $pages = [];

    /**
     * Pagination constructor.
     *
     * // @todo why is this passed in Project and an array of item keys. It should pass a list of the Sources!
     *
     * @param array   $items
     * @param int     $totalPages
     * @param int     $currentPage
     */
    public function __construct(array $items = [], $totalPages = 0, $currentPage = 0)
    {
        $this->setPages($items);
        $this->totalPages = $totalPages;
        $this->currentPage = $currentPage;
    }

    /**
     * @deprecated use getPages()
     * @return array|SourceInterface[]
     */
    public function getItems()
    {
        return $this->pages;
    }

    /**
     * @param null|SourceInterface $previous
     * @param null|SourceInterface $next
     */
    public function setPreviousNext(SourceInterface $previous = null, SourceInterface $next = null)
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
        return is_null($this->next) ? null : new ViewFile($this->next);
    }

    /**
     * Get the previous page, or null if we are the first page.
     *
     * @return null|ViewFile
     */
    public function getPrevious()
    {
        return is_null($this->previous) ? null : new ViewFile($this->previous);
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
     * @param array|SourceInterface[] $pages
     */
    public function setPages(array $pages = [])
    {
        foreach ($pages as $file) {
            array_push($this->pages, new ViewFile($file));
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
