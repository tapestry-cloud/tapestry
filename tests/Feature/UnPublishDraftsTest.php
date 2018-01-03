<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\TestCase;

class UnPublishDraftsTest extends TestCase
{
    /**
     * Written for issue #146
     * Tests that a file marked as a draft but with a date that is in the past is published.
     * @version 1.0.9
     * @link https://github.com/carbontwelve/tapestry/issues/146
     */
    public function testScheduledPosts()
    {
        $this->loadToTmp($this->assetPath('build_test_12/src'));
        $output = $this->runCommand('build', '--quiet --auto-publish');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileExists($this->tmpPath('build_local/blog/2016/test-blog-entry-two/index.html'));
    }

    /**
     * Test that setting draft to true in a blog posts front matter ensures that it is not published.
     */
    public function testUnPublishDrafts()
    {
        $this->loadToTmp($this->assetPath('build_test_12/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_12/check/blog/2016/test-blog-entry.html'),
            $this->tmpPath('build_local/blog/2016/test-blog-entry/index.html'),
            '',
            true
        );
        $this->assertFileNotExists($this->tmpPath('build_local/blog/2016/test-blog-entry-two/index.html'));
        $this->assertFileNotExists($this->tmpPath('build_local/blog/2116/test-blog-entry-three/index.html'));
    }

    /**
     * Test Configuration.
     */
    public function testPublishDraftsConfigurationOverride()
    {
        $this->loadToTmp($this->assetPath('build_test_13/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_13/check/blog/2016/test-blog-entry.html'),
            $this->tmpPath('build_local/blog/2016/test-blog-entry/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_13/check/blog/2016/test-blog-entry-two.html'),
            $this->tmpPath('build_local/blog/2016/test-blog-entry-two/index.html'),
            '',
            true
        );
    }
}
