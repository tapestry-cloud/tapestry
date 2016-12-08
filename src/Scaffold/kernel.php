<?php

namespace Site;

use Tapestry\Tapestry;
use Tapestry\Modules\Kernel\KernelInterface;

class kernel implements KernelInterface
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

    public function boot()
    {
        // ...
    }
}
