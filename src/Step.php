<?php

namespace Tapestry;

use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

interface Step
{
    /**
     * Process the Project at current.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output);
}
