<?php

namespace SiteEleven;

use Interop\Container\ContainerInterface;
use Tapestry\Tapestry;

class kernel implements \Tapestry\Kernel\KernelInterface
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    public function __construct()
    {
        $this->tapestry = Tapestry::getInstance();
    }

    public function register()
    {
        // ...
    }

    /**
     * @param ContainerInterface $container
     */
    public function boot(ContainerInterface $container)
    {
        // Not the ideal way of adding the file, but this a test so autoloading is not required :)
        include __DIR__.'/TestKernelCommand.php';

        /** @var \Tapestry\Console\Application $cliApplication */
        $cliApplication = $container->get(\Tapestry\Console\Application::class);
        $cliApplication->add(new \SiteEleven\TestKernelCommand());
    }
}
