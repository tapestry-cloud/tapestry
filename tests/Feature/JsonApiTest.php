<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\TestCase;

class JsonApiTest extends TestCase
{
    public function testJsonBuildFlag()
    {
        $this->loadToTmp($this->assetPath('build_test_1/src'));
        $output = $this->runCommand('build', '--quiet --json');

        $this->assertEquals(0, $output->getStatusCode());
        $this->assertFileExists($this->tmpPath('db.json'));
    }

    public function testNoWriteBuildFlag()
    {
        $this->loadToTmp($this->assetPath('build_test_1/src'));
        $output = $this->runCommand('build', '--quiet --json --no-write');

        $this->assertEquals(0, $output->getStatusCode());
        $this->assertFileExists($this->tmpPath('db.json'));
        $this->assertFileNotExists($this->tmpPath('build_local'));
    }
}
