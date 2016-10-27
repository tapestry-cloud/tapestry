<?php namespace Tapestry\Modules\Config;

use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Step;

class LoadConfig implements Step
{
    /**
     * If configuration exists, load it from the site and merge it with the default configuration for Tapestry The
     * defaults set must exist for Tapestry to function correctly.
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

        $configuration = new Configuration(include(__DIR__ . DIRECTORY_SEPARATOR . 'DefaultConfig.php'));
        $configuration->merge(include($configPath));
        $project->set('config', $configuration);

        $project->getOutput()->writeln('Loaded Config From ['. $configPath .']');
        return true;
    }
}
