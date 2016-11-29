<?php

namespace Tapestry\Tests;

class UnPublishDraftsTest extends CommandTestBase
{
    /**
     * Test that setting draft to true in a blog posts front matter ensures that it is not published.
     */
    public function testUnpublishDrafts()
    {
        $this->copyDirectory('assets/build_test_12/src', '_tmp');

        $output = $this->runCommand('build', ['--quiet']);

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_11/check/blog/2016/test-blog-entry.html',
            __DIR__.'/_tmp/build_local/blog/2016/test-blog-entry/index.html',
            '',
            true
        );
        self::assertFileNotExists(__DIR__.'/_tmp/build_local/blog/2016/test-blog-entry-two/index.html');
    }

    /**
     * Test Configuration.
     */
    public function testPublishDraftsConfigurationOverride()
    {
        $this->copyDirectory('assets/build_test_13/src', '_tmp');

        $output = $this->runCommand('build', ['--quiet']);

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_11/check/blog/2016/test-blog-entry.html',
            __DIR__.'/_tmp/build_local/blog/2016/test-blog-entry/index.html',
            '',
            true
        );

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_11/check/blog/2016/test-blog-entry-two.html',
            __DIR__.'/_tmp/build_local/blog/2016/test-blog-entry-two/index.html',
            '',
            true
        );
    }
}
