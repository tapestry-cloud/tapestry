<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\TestCase;

class MarkdownLayoutTest extends TestCase
{
    public function testMarkdownFilesGetRenderedInLayouts()
    {
        $this->loadToTmp($this->assetPath('build_test_19/src'));
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_19/check/test.html'),
            $this->tmpPath('build_local/test/index.html'),
            '',
            true
        );
    }

    public function testMarkdownFilesGetRenderedInChildLayouts()
    {
        $this->loadToTmp($this->assetPath('build_test_20/src'));
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_20/check/test.html'),
            $this->tmpPath('build_local/test/index.html'),
            '',
            true
        );
    }
}
