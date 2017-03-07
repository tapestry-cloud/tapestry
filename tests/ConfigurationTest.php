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

    /**
     * Test that YAML configuration is loaded.
     */
    public function testYAMLConfigurationDefault()
    {
        // @todo
    }

    /**
     * Test that the correct YAML file gets parsed when the --env attribute is filled
     */
    public function testYAMLEnvironmentConfigurationWithEnvSet()
    {
        // @todo
    }

    /**
     * If both a YAML and a PHP array configuration file exist within the workspace then Tapestry should exit with an
     * error code and appropriate message.
     */
    public function testYAMLandPHPConfigurationThrowsError()
    {
        // @todo
    }
}
