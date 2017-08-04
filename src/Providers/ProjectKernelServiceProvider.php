<?php

namespace Tapestry\Providers;

use Exception;
use Tapestry\Tapestry;
use Tapestry\Entities\Configuration;
use Tapestry\Modules\Kernel\DefaultKernel;
use Tapestry\Modules\Kernel\KernelInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class ProjectKernelServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * @var array
     */
    protected $provides = [
        KernelInterface::class,
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
     * @return void
     * @throws Exception
     */
    public function boot()
    {
        $container = $this->getContainer();

        /** @var Tapestry $tapestry */
        $tapestry = $container->get(Tapestry::class);
        $configuration = $container->get(Configuration::class);
        $kernelPath = $tapestry['currentWorkingDirectory'].DIRECTORY_SEPARATOR.'kernel.php';

        if (! file_exists($kernelPath)) {
            $kernelPath = $tapestry['currentWorkingDirectory'].DIRECTORY_SEPARATOR.'Kernel.php';
        }

        if (file_exists($kernelPath)) {
            $kernelClassName = $configuration->get('kernel', DefaultKernel::class);

            if (! class_exists($kernelClassName)) {
                include $kernelPath;
            }

            if (! class_exists($kernelClassName)) {
                throw new Exception('['.$kernelClassName.'] kernel file not found.');
            }

            $container->share(KernelInterface::class, $kernelClassName)->withArgument(
                $container->get(Tapestry::class)
            );
        } else {
            $container->share(KernelInterface::class, DefaultKernel::class)->withArgument(
                $container->get(Tapestry::class)
            );
        }

        /** @var KernelInterface $kernel */
        $kernel = $container->get(KernelInterface::class);
        $kernel->register();
    }
}
