<?php

namespace Tapestry\Steps;

use Tapestry\Entities\Configuration;
use Tapestry\Entities\DependencyGraph\SimpleNode;
use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Tapestry;

class LoadGraph implements Step
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * LoadGraph constructor.
     *
     * @param Tapestry $tapestry
     * @param Configuration $configuration
     */
    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->tapestry = $tapestry;
        $this->configuration = $configuration;
    }

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Exception
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $graph = $project->getGraph();

        /** @var KernelInterface $kernel */
        $kernel = $this->tapestry->getContainer()->get(KernelInterface::class);

        $reflection = new \ReflectionClass($kernel);
        $graph->setRoot(new SimpleNode('kernel', sha1_file($reflection->getFileName())));
        $graph->addEdge('kernel', new SimpleNode('configuration', sha1(json_encode($this->configuration->all()))));

        return true;
    }
}
