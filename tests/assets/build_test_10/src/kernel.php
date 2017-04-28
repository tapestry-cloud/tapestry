<?php

namespace SiteTen;

use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Tapestry;

class kernel implements KernelInterface
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
        // Not the ideal way of adding the file, but this a test so auto-loading is not necessary :)
        include __DIR__.'/TestKernelCommand.php';

        /** @var \Tapestry\Console\Application $cliApplication */
        $cliApplication = $this->tapestry->getContainer()->get(\Tapestry\Console\Application::class);
        $cliApplication->add(new TestKernelCommand());
    }

    public function boot()
    {
        // ...
    }
}
