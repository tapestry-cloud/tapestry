<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Cache;
use Tapestry\Entities\Project;
use Tapestry\Step;

class ReadCache implements Step
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
        $cache = new Cache($project->currentWorkingDirectory . DIRECTORY_SEPARATOR . '.' . $project->environment .'_cache');
        $cache->load();
        $project->set('cache', $cache);
        return true;
    }
}