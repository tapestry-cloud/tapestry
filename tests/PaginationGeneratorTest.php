<?php

namespace Tapestry\Tests;

class PaginationGeneratorTest extends CommandTestBase
{
    public function testPaginationRegression()
    {
        $this->copyDirectory('assets/build_test_17/src', '_tmp');
        $output = $this->runCommand('build', ['--quiet']);
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileExists(__DIR__.'/_tmp/build_local/blog/index.html');
        $this->assertFileExists(__DIR__.'/_tmp/build_local/blog/2/index.html');
    }
}
