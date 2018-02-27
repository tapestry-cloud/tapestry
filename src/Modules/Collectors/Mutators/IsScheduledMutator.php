<?php

namespace Tapestry\Modules\Collectors\Mutators;

use DateTime;
use Tapestry\Modules\Source\SourceInterface;

/**
 * Class IsScheduledMutator
 *
 * This mutator takes an input SourceInterface and determines if it is scheduled for publishing.
 * A scheduled source is one that has its draft flag set to `true` while also having a date set
 * that is less than or equal to `$now`.
 *
 *
 *
 * @package Tapestry\Modules\Collectors\Mutators
 */
final class IsScheduledMutator implements MutatorInterface
{
    /**
     * @var DateTime
     */
    private $now;

    /**
     * @var bool
     */
    private $canPublishDrafts;

    /**
     * @var bool
     */
    private $autoPublish;

    /**
     * IsScheduledMutator constructor.
     *
     * @todo maybe have the site config pass in here rather than variables that require external bootstrapping
     *
     * @param bool $canPublishDrafts
     * @param bool $autoPublish
     */
    public function __construct(bool $canPublishDrafts = false, bool $autoPublish = false)
    {
        $this->now = new DateTime();
        $this->autoPublish = $autoPublish;
        $this->canPublishDrafts = $canPublishDrafts;
    }

    public function mutate(SourceInterface &$source)
    {
        // Publish Drafts / Scheduled Posts
        if ($this->canPublishDrafts === false) {
            // If file is a draft and cant auto publish then it remains a draft
            if (
                boolval($source->getData('draft', false)) === true &&
                $this->canAutoPublish($source) === false
            ) {
                return;
            }

            // While the source's front matter says its a draft its publish date is
            // less than or equal to now meaning its "scheduled".
            $source->setData('draft', false);
        }
    }

    /**
     * If the file is a draft, but auto publish is enabled and the files date is in the past then it should be published.
     *
     * @param SourceInterface $source
     * @version 1.0.9
     * @return bool
     */
    private function canAutoPublish(SourceInterface $source)
    {
        if ($this->autoPublish === false) {
            return false;
        }

        if ($source->getData('date', new \DateTime()) <= $this->now) {
            return true;
        }

        return false;
    }
}