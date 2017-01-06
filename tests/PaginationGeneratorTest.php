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

    public function testPaginationNextPrevious()
    {
        $this->copyDirectory('assets/build_test_17/src', '_tmp');
        $output = $this->runCommand('build', ['--quiet']);
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_17/check/1.html',
            __DIR__.'/_tmp/build_local/blog/index.html',
            '',
            true
        );

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_17/check/2.html',
            __DIR__.'/_tmp/build_local/blog/2/index.html',
            '',
            true
        );
    }
}
