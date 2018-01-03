<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;

class JsonApiTest extends CommandTestBase
{
    public function testJsonBuildFlag()
    {
        $this->copyDirectory('assets/build_test_1/src', '_tmp');
        $output = $this->runCommand('build', '--quiet --json');
        $this->assertEquals(0, $output->getStatusCode());
        $this->assertFileExists(__DIR__ . '/_tmp/db.json');
    }

    public function testNoWriteBuildFlag()
    {
        $this->copyDirectory('assets/build_test_1/src', '_tmp');
        $output = $this->runCommand('build', '--quiet --json --no-write');
        $this->assertEquals(0, $output->getStatusCode());
        $this->assertFileExists(__DIR__ . '/_tmp/db.json');
        $this->assertFileNotExists(__DIR__ . '/_tmp/build_local');
    }
}
