<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\TestCase;

class ContentTypeTest extends TestCase
{
    public function testContentTypeTaxonomyDefaultsSetOnFiles()
    {
        $this->loadToTmp($this->assetPath('build_test_16/src'));
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_16/check/index.html'),
            $this->tmpPath('build_local/index.html'),
            '',
            true
        );
    }

    public function testPreviousNextOrder()
    {
        $this->loadToTmp($this->assetPath('build_test_18/src'));
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_18/check/first-post.html'),
            $this->tmpPath('build_local/blog/2015/first-post/index.html'),
            '',
            true
        );
        $this->assertFileEquals(
            $this->assetPath('build_test_18/check/second-post.html'),
            $this->tmpPath('build_local/blog/2015/second-post/index.html'),
            '',
            true
        );
        $this->assertFileEquals(
            $this->assetPath('build_test_18/check/third-post.html'),
            $this->tmpPath('build_local/blog/2015/third-post/index.html'),
            '',
            true
        );
        $this->assertFileEquals(
            $this->assetPath('build_test_18/check/fourth-post.html'),
            $this->tmpPath('build_local/blog/2015/fourth-post/index.html'),
            '',
            true
        );
    }
}
