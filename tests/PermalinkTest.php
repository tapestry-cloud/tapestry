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
     * @param File $file
     * @return Permalink
     */
    private function setupPermalinks(File $file)
    {
        return $file->getCompiledPermalink();
    }

    /**
     * @param string $filePath
     * @return File
     */
    private function setupFile($filePath)
    {
        $file = new File(new SplFileInfo($filePath, '', ''));
        $frontMatter = new FrontMatter($file->getFileContent());
        $file->setData($frontMatter->getData());
        $file->setContent($frontMatter->getContent());
        return $file;
    }

    /**
     * Written for issue #165
     * @link https://github.com/carbontwelve/tapestry/issues/165
     */
    public function testCategoryPermalinkTag()
    {
        // Synthetic Test
        $this->assertEquals('/category1/category2/category3/test-md-post/index.html', $this->setupPermalinks($this->setupFile(__DIR__ . '/mocks/TestCategoryPermalinkTag.md')));

        // Full Test
        $this->copyDirectory('assets/build_test_33/src', '_tmp');
        $output = $this->runCommand('build', '--quiet --json');
        $this->assertEquals(0, $output->getStatusCode());
        $this->assertFileExists(__DIR__ . '/_tmp/build_local/blog/2016/category-1/category-iii/category-two/test/index.html');
    }

    /**
     * Written for issue #241
     * @link https://github.com/carbontwelve/tapestry/issues/241
     */
    public function testCategoryPermalinkTagWithLimit()
    {
        $this->assertEquals('/category1/test-md-post/index.html', $this->setupPermalinks($this->setupFile(__DIR__ . '/mocks/TestCategoryPermalinkTagLimitOne.md')));
        $this->assertEquals('/category1/category2/test-md-post/index.html', $this->setupPermalinks($this->setupFile(__DIR__ . '/mocks/TestCategoryPermalinkTagLimitTwo.md')));
    }

    public function testPrettyPermalink()
    {
        $this->assertEquals('/testfile/index.md', $this->setupPermalinks($this->setupFile(__DIR__ . '/mocks/TestFile.md')));
    }

    public function testPermalinkPathSlashes()
    {
        $file = $this->setupFile(__DIR__ . '/mocks/TestFile.md');

        $backSlashTest = $file;
        $backSlashTest->setPath('hello\\world/123');
        $this->assertEquals('/hello/world/123/testfile/index.md', $this->setupPermalinks($backSlashTest));

        $beginningSlashTest = $file;
        $beginningSlashTest->setPath('/hello/world/123');
        $this->assertEquals('/hello/world/123/testfile/index.md', $this->setupPermalinks($beginningSlashTest));

        $endingSlashTest = $file;
        $endingSlashTest->setPath('/hello/world/123/');
        $this->assertEquals('/hello/world/123/testfile/index.md', $this->setupPermalinks($endingSlashTest));

        $doubleSlashTest = $file;
        $doubleSlashTest->setPath('hello//world\\123/');
        $this->assertEquals('/hello/world/123/testfile/index.md', $this->setupPermalinks($doubleSlashTest));
    }
}
