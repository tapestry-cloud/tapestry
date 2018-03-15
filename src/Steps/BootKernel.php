<?php

namespace Tapestry\Steps;

use Tapestry\Entities\Tree\Leaf;
use Tapestry\Entities\Tree\Symbol;
use Tapestry\Entities\Tree\Tree;
use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Step;
use Tapestry\Tapestry;
use Tapestry\Entities\Project;
use Tapestry\Entities\Configuration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BootKernel
 *
 * This Step identifies the configured Kernel for this Project and executes its `boot()` method.
 */
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
     * @throws \ReflectionException
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->tapestry->getContainer()->get(KernelInterface::class);
        $kernel->boot();

        $reflection = new \ReflectionClass($kernel);

        if ($mTime = filemtime($reflection->getFileName())) {
            $kernelSymbol = new Symbol('kernel', Symbol::SYMBOL_KERNEL, $mTime);
        } else {
            $kernelSymbol = new Symbol('kernel', Symbol::SYMBOL_KERNEL, -1);
            $kernelSymbol->setHash(sha1_file($reflection->getFileName()));
        }

        /** @var Tree $tree */
        $tree = $project['ast'];
        $tree->add(new Leaf('kernel', $kernelSymbol));

        return true;
    }
}
