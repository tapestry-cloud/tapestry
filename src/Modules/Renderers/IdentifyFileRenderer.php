<?php namespace Tapestry\Modules\Renderers;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Step;

class IdentifyFileRenderer implements Step
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
        /** @var File $file */
        foreach ($project['files']->all() as $file) {
            $n = 1;
        }
    }
}
