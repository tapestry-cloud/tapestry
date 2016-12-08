<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class ParseFileTemplates implements Step
{
    /**
     * For each file with a template render the page content and then place it within the template.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        return true;
    }
}
