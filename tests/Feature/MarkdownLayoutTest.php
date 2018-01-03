<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;

class MarkdownLayoutTest extends CommandTestBase
{
    public function testMarkdownFilesGetRenderedInLayouts()
    {
        $this->copyDirectory('assets/build_test_19/src', '_tmp');
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_19/check/test.html',
            __DIR__.'/_tmp/build_local/test/index.html',
            '',
            true
        );
    }

    public function testMarkdownFilesGetRenderedInChildLayouts()
    {
        $this->copyDirectory('assets/build_test_20/src', '_tmp');
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_20/check/test.html',
            __DIR__.'/_tmp/build_local/test/index.html',
            '',
            true
        );
    }
}
