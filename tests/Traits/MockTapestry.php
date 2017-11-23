<?php

namespace Tapestry\Tests\Traits;

use Symfony\Component\Console\Input\ArrayInput;
use Tapestry\Console\DefaultInputDefinition;
use Tapestry\Entities\Configuration;
use Tapestry\Tapestry;

trait MockTapestry {
    /**
     * "Mock" Tapestry.
     *
     * @param null $siteDir
     * @param null|array $configuration
     * @param string $environment
     * @return Tapestry
     */
    protected function mockTapestry($siteDir = null, $configuration = null, $environment = 'testing')
    {
        if (is_null($siteDir)) {
            $siteDir = __DIR__ . DIRECTORY_SEPARATOR . '_tmp';
        }

        $definitions = new DefaultInputDefinition();
        $tapestry = new Tapestry(new ArrayInput([
            '--site-dir' => $siteDir,
            '--env' => $environment
        ], $definitions));

        if (is_array($configuration)) {
            $tapestry->getContainer()->add(Configuration::class, new Configuration($configuration));
        }

        return $tapestry;
    }
}