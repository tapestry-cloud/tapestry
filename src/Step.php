<?php

namespace Tapestry;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Project;

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
