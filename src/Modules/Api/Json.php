<?php

namespace Tapestry\Modules\Api;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class Json implements Step
{

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        if ($project->get('cmd_options.json') !== true) {
            return true;
        }

        $output->writeln('[+] Exporting JSON Blob');

    }
}