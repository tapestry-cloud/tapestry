<?php

namespace Tapestry\Modules\Kernel;

use Tapestry\Step;
use Tapestry\Tapestry;
use Tapestry\Entities\Project;
use Tapestry\Entities\Configuration;
use Symfony\Component\Console\Output\OutputInterface;

class BootKernel implements Step
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * @var Configuration
     */
    private $configuration;

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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->tapestry->getContainer()->get(KernelInterface::class);
        $kernel->boot();

        return true;
    }
}
