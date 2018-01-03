<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    public function testEnvironmentConfigurationDefault()
    {
        $this->loadToTmp($this->assetPath('build_test_14/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_14/check/default_index.html'),
            $this->tmpPath('build_local/index.html'),
            '',
            true
        );
    }

    public function testEnvironmentConfigurationWithEnvSet()
    {
        $this->loadToTmp($this->assetPath('build_test_14/src'));
        $output = $this->runCommand('build', '--quiet --env=development');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_14/check/development_index.html'),
            $this->tmpPath('build_development/index.html'),
            '',
            true
        );
    }

    /**
     * Test that YAML configuration is loaded.
     */
    public function testYAMLConfigurationDefault()
    {
        $this->loadToTmp($this->assetPath('build_test_26/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_26/check/index.html'),
            $this->tmpPath('build_local/index.html'),
            '',
            true
        );
    }

    /**
     * Test that the correct YAML file gets parsed when the --env attribute is filled
     */
    public function testYAMLEnvironmentConfigurationWithEnvSet()
    {
        $this->loadToTmp($this->assetPath('build_test_26/src'));
        $output = $this->runCommand('build', '--quiet --env=development');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_26/check/development_index.html'),
            $this->tmpPath('build_development/index.html'),
            '',
            true
        );
    }

    /**
     * If both a YAML and a PHP array configuration file exist within the workspace then Tapestry should exit with an
     * error code and appropriate message.
     *
     * @expectedException \Exception
     */
    public function testYAMLandPHPConfigurationThrowsError()
    {
        $this->loadToTmp($this->assetPath('build_test_27/src'));
        $this->runCommand('build', '--quiet');
    }

    /**
     * @expectedException \Exception
     */
    public function testYAMLandPHPConfigurationWithEnvSetThrowsError()
    {
        $this->loadToTmp($this->assetPath('build_test_27/src'));
        $this->runCommand('build', '--quiet --env=development');
    }
}
