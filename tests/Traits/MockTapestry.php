<?php

namespace Tapestry\Tests\Traits;

use Symfony\Component\Console\Input\ArrayInput;
use Tapestry\Console\DefaultInputDefinition;
use Tapestry\Tapestry;

trait MockTapestry {
    /**
     * "Mock" Tapestry.
     *
     * @param null $siteDir
     * @return Tapestry
     */
    protected function mockTapestry($siteDir = null)
    {
        if (is_null($siteDir)) {
            $siteDir = __DIR__ . DIRECTORY_SEPARATOR . '_tmp';
        }

        $definitions = new DefaultInputDefinition();
        $tapestry = new Tapestry(new ArrayInput([
            '--site-dir' => $siteDir,
            '--env' => 'testing'
        ], $definitions));

        return $tapestry;
    }
}