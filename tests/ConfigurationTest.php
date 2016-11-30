<?php

namespace Tapestry\Tests;

class ConfigurationTest extends CommandTestBase
{
    public function testEnvironmentConfigurationDefault()
    {
        $this->copyDirectory('assets/build_test_14/src', '_tmp');

        $output = $this->runCommand('build', ['--quiet']);

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_14/check/default_index.html',
            __DIR__.'/_tmp/build_local/index.html',
            '',
            true
        );
    }

    public function testEnvironmentConfigurationWithEnvSet()
    {
        $this->copyDirectory('assets/build_test_14/src', '_tmp');

        $output = $this->runCommand('build', ['--quiet', '--env' => 'development']);

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_14/check/development_index.html',
            __DIR__.'/_tmp/build_development/index.html',
            '',
            true
        );
    }
}
