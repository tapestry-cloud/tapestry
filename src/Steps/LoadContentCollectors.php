<?php

namespace Tapestry\Steps;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Project;
use Tapestry\Step;

/**
 * Class LoadContentCollectors
 *
 * This Step looks up the configured collectors and fills the `collectors` object
 * for the given Project. This is required to have been invoked after
 * LoadContentTypes because it excludes their paths from `IsIgnoredMutator`.
 *
 * @package Tapestry\Steps
 */
class LoadContentCollectors implements Step
{

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output): Bool
    {
        return false;
    }
}