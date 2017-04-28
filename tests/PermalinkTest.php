<?php

namespace Tapestry\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Entities\Generators\PaginationGenerator;
use Tapestry\Entities\Pagination;
use Tapestry\Entities\Permalink;
use Tapestry\Entities\Project;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;

class PermalinkTest extends CommandTestBase
{
    /**
     * @param $filePath
     * @return Permalink
     */
    private function setupPermalinks($filePath)
    {
        $file = new File(new SplFileInfo($filePath, '', ''));
        $frontMatter = new FrontMatter($file->getFileContent());
        $file->setData($frontMatter->getData());
        $file->setContent($frontMatter->getContent());
        return $file->getCompiledPermalink();
    }

    /**
     * Written for issue #165
     * @link https://github.com/carbontwelve/tapestry/issues/165
     */
    public function testCategoryPermalinkTag()
    {
        $this->assertEquals('/category1/category2/category3/test-md-post/index.html', $this->setupPermalinks(__DIR__ . '/mocks/TestCategoryPermalinkTag.md'));
    }

    public function testPrettyPermalink()
    {
        $this->assertEquals('/testfile/index.md', $this->setupPermalinks(__DIR__ . '/mocks/TestFile.md'));
    }
}
