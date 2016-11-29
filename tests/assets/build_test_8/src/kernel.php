<?php

namespace SiteNine;

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
        $this->tapestry->getContainer()->get(\Tapestry\Content\Configuration::class)->set('kernel_register_works', 'Kernel Registering Works');
    }

    /**
     * @param ContainerInterface $container
     *
     * @throws \Exception
     */
    public function boot(ContainerInterface $container)
    {
        $this->tapestry->getContainer()->get(\Tapestry\Content\Configuration::class)->set('kernel_boot_works', 'Kernel Booting Works');
    }
}
