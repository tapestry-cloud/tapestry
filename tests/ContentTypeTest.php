<?php

namespace Tapestry\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Tests\Iterator\MockSplFileInfo;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;

class ContentTypeTest extends CommandTestBase
{
    public function testContentTypeTaxonomyDefaultsSetOnFiles()
    {
        $this->copyDirectory('assets/build_test_16/src', '_tmp');
        $output = $this->runCommand('build', ['--quiet']);
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_16/check/index.html',
            __DIR__.'/_tmp/build_local/index.html',
            '',
            true
        );
    }

    public function testPreviousNextOrder(){
        $this->copyDirectory('assets/build_test_18/src', '_tmp');
        $output = $this->runCommand('build', ['--quiet']);
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_18/check/first-post.html',
            __DIR__.'/_tmp/build_local/blog/2015/first-post/index.html',
            '',
            true
        );
        $this->assertFileEquals(
            __DIR__.'/assets/build_test_18/check/second-post.html',
            __DIR__.'/_tmp/build_local/blog/2015/second-post/index.html',
            '',
            true
        );
        $this->assertFileEquals(
            __DIR__.'/assets/build_test_18/check/third-post.html',
            __DIR__.'/_tmp/build_local/blog/2015/third-post/index.html',
            '',
            true
        );
        $this->assertFileEquals(
            __DIR__.'/assets/build_test_18/check/fourth-post.html',
            __DIR__.'/_tmp/build_local/blog/2015/fourth-post/index.html',
            '',
            true
        );
    }

    public function testAddFileMutatesFileDataWithContentTypeName()
    {
        $contentType = new ContentType('Test', ['enabled' => true]);
        $file = new File(new SplFileInfo(__DIR__ . '/mocks/TestFile.md', '', ''));
        $this->assertFalse($file->hasData('contentType'));
        $contentType->addFile($file);
        $this->assertTrue($file->hasData('contentType'));
    }
}
