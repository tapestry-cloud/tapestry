<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Project;
use Tapestry\Step;

class Write implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        // TODO: Implement __invoke() method.
    }
}
