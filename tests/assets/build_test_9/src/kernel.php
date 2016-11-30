<?php

namespace SiteTen;

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
        $this->tapestry->getContainer()->get(\Tapestry\Content\Configuration::class)->set('kernel_works', 'Hello world, this is the kernel speaking!');
    }

    /**
     * @param ContainerInterface $container
     */
    public function boot(ContainerInterface $container)
    {
        // ...
    }
}
