<?php namespace Tapestry\Modules\Config;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Step;
use Tapestry\Tapestry;

class LoadConfig implements Step
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    public function __construct(Tapestry $tapestry)
    {
        $this->tapestry = $tapestry;
    }

    /**
     * If configuration exists, load it from the site and merge it with the default configuration for Tapestry The
     * defaults set must exist for Tapestry to function correctly.
     *
     * @param Project $project
     * @param OutputInterface $output
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $configPath = $project->currentWorkingDirectory . DIRECTORY_SEPARATOR . 'config.php';

        if (! file_exists($configPath)){
            $output->writeln('[!] The config file could not be opened at ['.$configPath.']');
            return false;
        }

        $configuration = new Configuration(include(__DIR__ . DIRECTORY_SEPARATOR . 'DefaultConfig.php'));
        $configuration->merge(include($configPath));

        $this->tapestry->getContainer()->share(Configuration::class, $configuration);
        $output->writeln('Loaded Config From ['. $configPath .']');
        return true;
    }
}
