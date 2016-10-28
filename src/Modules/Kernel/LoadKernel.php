<?php namespace Tapestry\Modules\Kernel;

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

    public function __construct(Tapestry $tapestry)
    {
        $this->tapestry = $tapestry;
    }

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @return mixed
     */
    public function __invoke(Project $project)
    {
        $kernelPath = $project->get('cwd') . DIRECTORY_SEPARATOR . 'kernel.php';

        /** @var Configuration $configuration */
        $configuration = $project->get('config');

        // Should we warn the user if the kernel.php exists but their configuration is malformed?
        if (file_exists($kernelPath)){
            include $kernelPath;
            $this->tapestry->getContainer()->share(KernelInterface::class, $configuration->get('kernel', DefaultKernel::class));
        }else{
            $this->tapestry->getContainer()->share(KernelInterface::class, DefaultKernel::class);
        }

        /** @var KernelInterface $kernel */
        $kernel = $this->tapestry->getContainer()->get(KernelInterface::class);
        $kernel->boot();

        return true;
    }
}
