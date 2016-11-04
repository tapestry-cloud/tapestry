<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\Project;
use Tapestry\Step;

class ParseFileTemplates implements Step
{
    /**
     * For each file with a template render the page content and then place it within the template
     *
     * @param Project $project
     * @param OutputInterface $output
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        return true;
    }
}
