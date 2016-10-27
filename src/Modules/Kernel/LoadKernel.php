<?php namespace Tapestry\Modules\Kernel;

use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Step;

class LoadKernel implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @return mixed
     */
    public function __invoke(Project $project)
    {
        $kernelPath = $project->get('cwd') . DIRECTORY_SEPARATOR . 'kernel.php';
        $ioc = $project->getTapestry()->getContainer();

        /** @var Configuration $configuration */
        $configuration = $project->get('config');

        // Should we warn the user if the kernel.php exists but their configuration is malformed?
        if (file_exists($kernelPath)){
            include $kernelPath;
            $ioc->share(KernelInterface::class, $configuration->get('kernel', DefaultKernel::class));
        }else{
            $ioc->share(KernelInterface::class, DefaultKernel::class);
        }

        /** @var KernelInterface $kernel */
        $kernel = $ioc->get(KernelInterface::class);
        $kernel->boot();

        return true;
    }
}
