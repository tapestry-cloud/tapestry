<?php

namespace Tapestry\Modules\Generators;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Step;
use Tapestry\Tapestry;

class LoadContentGenerators implements Step
{
    /**
     * @var \League\Container\ContainerInterface
     */
    private $container;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->container = $tapestry->getContainer();
    }

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
        if (!$contentGenerators = $this->configuration->get('content_generators', null)) {
            $output->writeln('[!] Your project\'s content generators are miss-configured. Doing nothing and exiting.]');
            exit(1);
        }

        $project->set('content_generators', new ContentGeneratorFactory($contentGenerators));

        return true;
    }
}
