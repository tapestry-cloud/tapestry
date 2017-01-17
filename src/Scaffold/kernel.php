<?php

namespace Site;

use Tapestry\Tapestry;
use Tapestry\Modules\Kernel\KernelInterface;

class SiteKernel implements KernelInterface
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * DefaultKernel constructor.
     *
     * @param Tapestry $tapestry
     */
    public function __construct(Tapestry $tapestry)
    {
        $this->tapestry = $tapestry;
    }

    /**
     * This method is executed by Tapestry when the Kernel is registered.
     *
     * @return void
     */
    public function register()
    {
        // ...
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
