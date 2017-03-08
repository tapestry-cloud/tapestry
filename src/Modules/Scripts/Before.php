<?php

namespace Tapestry\Modules\Scripts;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class Before extends Script implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $this->tapestry->getEventEmitter()->emit('scripts.before');

        return true;
    }
}
