<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Entities\Cache;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Tapestry;

class ReadCache implements Step
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
        $hash = sha1(Tapestry::VERSION);

        $cache = new Cache($project->currentWorkingDirectory.DIRECTORY_SEPARATOR.'.'.$project->environment.'_cache', $hash);
        $cache->load();
        $project->set('cache', $cache);

        return true;
    }
}
