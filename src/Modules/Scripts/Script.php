<?php

namespace Tapestry\Modules\Scripts;

use Tapestry\Tapestry;

abstract class Script
{
    /**
     * @var Tapestry
     */
    protected $tapestry;

    /**
     * Before constructor.
     *
     * @param Tapestry $tapestry
     */
    public function __construct(Tapestry $tapestry)
    {
        $this->tapestry = $tapestry;
    }
}
