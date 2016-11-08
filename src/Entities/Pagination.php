<?php namespace Tapestry\Entities;

class Pagination
{
    /**
     * @var array
     */
    public $items;

    /**
     * @var int
     */
    public $totalPages;

    /**
     * @var int
     */
    public $currentPage;

    /**
     * Pagination constructor.
     * @param array $items
     * @param int $totalPages
     * @param int $currentPage
     */
    public function __construct(array $items = [], $totalPages = 0, $currentPage = 0)
    {
        $this->items = $items;
        $this->totalPages = $totalPages;
        $this->currentPage = $currentPage;
    }

}