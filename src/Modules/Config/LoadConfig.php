<?php namespace Tapestry\Modules\Config;

use Tapestry\Entities\Project;
use Tapestry\Step;

class LoadConfig implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @return mixed
     */
    public function __invoke(Project $project)
    {
        $configPath = $project->get('cwd') . DIRECTORY_SEPARATOR . 'config.php';

        if (! file_exists($configPath)){
            $project->getOutput()->writeln('[!] The config file could not be opened at ['.$configPath.']');
            return false;
        }

        $project->getOutput()->writeln('Loading Config From ['. $configPath .']');

        return true;
    }
}
