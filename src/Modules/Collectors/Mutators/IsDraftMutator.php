<?php

namespace Tapestry\Modules\Collectors\Mutators;

use DateTime;
use Tapestry\Modules\Source\SourceInterface;

final class IsDraftMutator implements MutatorInterface
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
     * IsDraftMutator constructor.
     *
     * @todo maybe have the site config pass in here rather than variables that require external bootstrapping
     *
     * @param bool $canPublishDrafts
     * @param bool $autoPublish
     */
    public function __construct(bool $canPublishDrafts = false, bool $autoPublish = false)
    {
        $this->now = new DateTime();

        $this->canPublishDrafts = $canPublishDrafts;
        $this->autoPublish = $autoPublish;
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

            // If file is not a draft, but the date is in the future then it is scheduled
            if ($source->getData('date', new \DateTime()) > $this->now) {
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