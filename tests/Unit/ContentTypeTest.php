<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;
use Tapestry\Tests\TestCase;

class ContentTypeTest extends TestCase
{

    /**
     * Added for issue 88
     * @see https://github.com/carbontwelve/tapestry/issues/88
     */
    public function testAddFileMutatesFileDataWithContentTypeName()
    {
        $contentType = new ContentType('Test', ['enabled' => true]);
        $file = new File(new SplFileInfo(__DIR__ . '/../Mocks/TestFile.md', '', ''));
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