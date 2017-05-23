<?php

namespace Tapestry\Tests\Traits;

use Symfony\Component\Console\Input\ArrayInput;
use Tapestry\Console\DefaultInputDefinition;
use Tapestry\Tapestry;

trait MockTapestry {
    /**
     * "Mock" Tapestry.
     *
     * @return Tapestry
     */
    protected function mockTapestry()
    {
        $definitions = new DefaultInputDefinition();
        $tapestry = new Tapestry(new ArrayInput([
            '--site-dir' => __DIR__ . DIRECTORY_SEPARATOR . '_tmp',
            '--env' => 'testing'
        ], $definitions));

        return $tapestry;
    }
}