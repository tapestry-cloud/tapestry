<?php namespace Tapestry\Entities;

class Pagination
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var int
     */
    public $totalPages;

    /**
     * @var int
     */
    public $currentPage;

    /**
     * @var Project
     */
    private $project;

    private $loaded = false;

    /**
     * Pagination constructor.
     * @param Project $project
     * @param array $items
     * @param int $totalPages
     * @param int $currentPage
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
        if ($this->loaded === true) { return $this->items; }

        array_walk_recursive($this->items, function(&$file, $fileKey){
            /** @var File $compiledFile */
            if (! $compiledFile = $this->project->get('compiled.' . $fileKey)) {
                $file = null;
            }else{
                $file = new ViewFile($this->project, $compiledFile->getUid());
            }
        });

        return $this->items;
    }

}