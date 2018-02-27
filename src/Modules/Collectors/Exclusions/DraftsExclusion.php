<?php

namespace Tapestry\Modules\Collectors\Exclusions;

use Tapestry\Modules\Source\SourceInterface;

/**
 * Class DraftsExclusion
 *
 * Filters out draft files if publishing drafts is not allowed and the file in question
 * is not considered "scheduled".
 *
 * @package Tapestry\Modules\Collectors\Exclusions
 */
class DraftsExclusion implements ExclusionInterface
{
    /**
     * 
     * @var bool 
     */
    private $canPublishDrafts;

    /**
     * DraftsExclusion constructor.
     *
     * @param bool $canPublishDrafts
     */
    public function __construct(bool $canPublishDrafts = false)
    {
        $this->canPublishDrafts = $canPublishDrafts;
    }

    /**
     * Returns whether the input SourceInterface should be excluded from the
     * Collectors output: true = exclude, false = include.
     *
     * @param SourceInterface $source
     * @return bool
     */
    public function filter(SourceInterface $source): bool
    {
        if ($this->canPublishDrafts){
            return false;
        }

        return $source->getData('draft', false);
    }
}