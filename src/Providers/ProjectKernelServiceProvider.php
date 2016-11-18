<?php namespace Tapestry\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Modules\Kernel\DefaultKernel;
use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Tapestry;

class ProjectKernelServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    /**
     * @var array
     */
    protected $provides = [
        KernelInterface::class
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     *
     * @return void
     */
    public function boot()
    {
        /** @var Tapestry $tapestry */
        $tapestry = $this->getContainer()->get(Tapestry::class);

        $configuration = $this->getContainer()->get(Configuration::class);

        $kernelPath = $tapestry['currentWorkingDirectory'] . DIRECTORY_SEPARATOR . 'kernel.php';

        if (file_exists($kernelPath)){
            include $kernelPath;
            $this->getContainer()->share(KernelInterface::class, $configuration->get('kernel', DefaultKernel::class))->withArgument(
                $this->getContainer()->get(Tapestry::class)
            );
        }else{
            $this->getContainer()->share(KernelInterface::class, DefaultKernel::class)->withArgument(
                $this->getContainer()->get(Tapestry::class)
            );
        }

        /** @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get(KernelInterface::class);
        $kernel->register();
    }
}