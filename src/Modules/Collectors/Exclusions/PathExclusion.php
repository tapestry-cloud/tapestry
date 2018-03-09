<?php

namespace Tapestry\Modules\Collectors\Exclusions;

use Tapestry\Modules\Source\SourceInterface;

/**
 * Class PathExclusion.
 *
 * Filters out files that match an defined path. This can be used to configure tapestry to ignore
 * certain paths and not parse the files inside.
 */
class PathExclusion implements ExclusionInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * PathExclusion constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Returns whether the input SourceInterface should be excluded from the
     * Collectors output.
     *
     * @param SourceInterface $source
     * @return bool
     */
    public function filter(SourceInterface $source): bool
    {
        if (strpos($source->getRelativePath(), $this->path) === false) {
            return false;
        }

        return true;
    }
}
