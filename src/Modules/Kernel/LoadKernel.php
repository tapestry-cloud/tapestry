<?php namespace Tapestry\Modules\Kernel;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Step;
use Tapestry\Tapestry;

class LoadKernel implements Step
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
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $kernelPath = $project->currentWorkingDirectory . DIRECTORY_SEPARATOR . 'kernel.php';

        // Should we warn the user if the kernel.php exists but their configuration is malformed?
        if (file_exists($kernelPath)){
            include $kernelPath;
            $this->tapestry->getContainer()->share(KernelInterface::class, $this->configuration->get('kernel', DefaultKernel::class))->withArgument(
                $this->tapestry->getContainer()->get(Tapestry::class)
            );
        }else{
            $this->tapestry->getContainer()->share(KernelInterface::class, DefaultKernel::class)->withArgument(
                $this->tapestry->getContainer()->get(Tapestry::class)
            );
        }

        /** @var KernelInterface $kernel */
        $kernel = $this->tapestry->getContainer()->get(KernelInterface::class);
        $kernel->boot();

        return true;
    }
}
