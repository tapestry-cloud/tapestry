<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\TestCase;

class PaginationGeneratorTest extends TestCase
{
    public function testPaginationRegression()
    {
        $this->loadToTmp($this->assetPath('build_test_17/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals(0, $output->getStatusCode());
        $this->assertFileExists($this->tmpPath('build_local/blog/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/blog/2/index.html'));
    }

    public function testPaginationNextPrevious()
    {
        $this->loadToTmp($this->assetPath('build_test_17/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_17/check/1.html'),
            $this->tmpPath('build_local/blog/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_17/check/2.html'),
            $this->tmpPath('build_local/blog/2/index.html'),
            '',
            true
        );
    }
}
