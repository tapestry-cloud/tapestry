<?php

namespace Tapestry\Tests;

class JsonApiTest extends CommandTestBase
{
    public function testJsonBuildFlag()
    {
        $this->copyDirectory('assets/build_test_1/src', '_tmp');
        $output = $this->runCommand('build', ['--quiet', '--json']);
        $this->assertEquals(0, $output->getStatusCode());
    }
}
