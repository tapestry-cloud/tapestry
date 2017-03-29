<?php

namespace SiteEight;

use Tapestry\Entities\Configuration;
use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Tapestry;

class Kernel implements KernelInterface
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
        $this->tapestry->getContainer()->get(Configuration::class)->set('site.kernel_register_works',
            'Kernel Registering Works');
    }

    public function boot()
    {
        $this->tapestry->getContainer()->get(Configuration::class)->set('site.kernel_boot_works',
            'Kernel Booting Works');
    }
}
