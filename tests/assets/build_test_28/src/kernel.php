<?php

namespace SiteTwentyEight;

use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Plates\Engine;
use Tapestry\Tapestry;
use TapestryCloud\Lib\TestPlatesExtension;

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

    /**
     * This method is executed by Tapestry when the Kernel is registered.
     *
     * @return void
     */
    public function register()
    {
        include (__DIR__ . '/lib/TestPlatesExtension.php');

        /** @var Engine $engine */
        $engine = $this->tapestry->getContainer()->get(Engine::class);
        $engine->loadExtension($this->tapestry->getContainer()->get(TestPlatesExtension::class));
    }

    /**
     * This method of executed by Tapestry as part of the build process.
     *
     * @return void
     */
    public function boot()
    {
        // ...
    }
}
