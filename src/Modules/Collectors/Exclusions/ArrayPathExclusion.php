<?php

namespace Tapestry\Modules\Collectors\Exclusions;

use Tapestry\Modules\Source\SourceInterface;

/**
 * Class ArrayPathExclusion
 *
 * Filters out files that match any paths in the ignored paths input array.
 *
 * @package Tapestry\Modules\Collectors\Exclusions
 */
class ArrayPathExclusion implements ExclusionInterface
{
    /**
     *
     * @var array|PathExclusion[]
     */
    private $ignorePaths = [];

    /**
     * ArrayPathExclusion constructor.
     *
     * @param array $ignorePaths
     */
    public function __construct(array $ignorePaths)
    {
        foreach ($ignorePaths as $ignorePath)
        {
            $this->ignorePaths[] = new PathExclusion($ignorePath);
        }
    }

    /**
     * @param SourceInterface $source
     * @return bool
     */
    public function filter(SourceInterface $source): bool
    {
        foreach ($this->ignorePaths as $item) {
            if ($item->filter($source) === true) { return true; }
        }
        return false;
    }
}