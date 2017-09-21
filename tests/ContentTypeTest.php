<?php

namespace Tapestry\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;

class ContentTypeTest extends CommandTestBase
{
    public function testContentTypeTaxonomyDefaultsSetOnFiles()
    {
        $this->copyDirectory('assets/build_test_16/src', '_tmp');
        $output = $this->runCommand('build', '--quiet');
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
        $output = $this->runCommand('build', '--quiet');
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

    /**
     * Added for issue 88
     * @see https://github.com/carbontwelve/tapestry/issues/88
     */
    public function testAddFileMutatesFileDataWithContentTypeName()
    {
        $contentType = new ContentType('Test', ['enabled' => true]);
        $file = new File(new SplFileInfo(__DIR__ . '/Mocks/TestFile.md', '', ''));
        $this->assertFalse($file->hasData('contentType'));
        $contentType->addFile($file);
        $this->assertTrue($file->hasData('contentType'));
    }
    
    /**
     * Added for issue 87
     * @see https://github.com/carbontwelve/tapestry/issues/87
     */
    public function testContentTypeFactoryArrayAccessByKey()
    {
        $contentType = new ContentType('Test', ['enabled' => true]);
        $contentTypeFactory = new ContentTypeFactory([
            $contentType
        ]);
        $this->assertTrue($contentTypeFactory->has('_Test'));
        $this->assertEquals($contentType, $contentTypeFactory->arrayAccessByKey('Test'));
        $this->assertEquals(null, $contentTypeFactory->arrayAccessByKey('NonExistant'));
    }
  
    /**
     * Added for issue 86
     * @see https://github.com/carbontwelve/tapestry/issues/86
     */
    public function testContentTypeEnabled()
    {
        $contentType = new ContentType('Test', []);
        $this->assertFalse($contentType->isEnabled());
        $contentType = new ContentType('Test', ['enabled' => true]);
        $this->assertTrue($contentType->isEnabled());
    }
}
