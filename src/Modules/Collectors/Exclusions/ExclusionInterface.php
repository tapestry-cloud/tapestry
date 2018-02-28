<?php

namespace Tapestry\Modules\Collectors\Exclusions;

use Tapestry\Modules\Source\SourceInterface;

interface ExclusionInterface
{
    /**
     * Returns whether the input SourceInterface should be excluded from the
     * Collectors output.
     *
     * Note: This method should return true if the SourceInterface
     * should be excluded.
     *
     * @param SourceInterface $source
     * @return bool
     */
    public function filter(SourceInterface $source): bool  ;
}