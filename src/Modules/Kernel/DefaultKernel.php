<?php

namespace Tapestry\Modules\Kernel;

use Tapestry\Tapestry;

class DefaultKernel implements KernelInterface
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

    public function register()
    {
        // ...
    }

    public function boot()
    {
        // ...
    }
}
