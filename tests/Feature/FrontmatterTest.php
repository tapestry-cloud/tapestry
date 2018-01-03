<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\File;
use Tapestry\Modules\Content\FrontMatter;

class FrontmatterTest extends CommandTestBase
{
    /**
     * Written for issue #148
     * @link https://github.com/carbontwelve/tapestry/issues/148
     */
    function testFrontmatterParsedWhenBodyEmpty()
    {
        $file = new File(new SplFileInfo(__DIR__ . '/Mocks/TestFileNoBody.md', '', ''));
        $frontMatter = new FrontMatter($file->getFileContent());
        $this->assertSame('', $frontMatter->getContent());
        $this->assertSame([
            'title' => 'Test File Title',
            'draft' => false,
            'date' => 507600000
        ], $frontMatter->getData());
    }

    function testFrontMatterAndBodyParsedCorrectly()
    {
        $file = new File(new SplFileInfo(__DIR__ . '/Mocks/TestFile.md', '', ''));
        $frontMatter = new FrontMatter($file->getFileContent());
        $this->assertSame('This is a test file...', $frontMatter->getContent());
        $this->assertSame([
            'title' => 'Test File Title',
            'draft' => false,
            'date' => 507600000
        ], $frontMatter->getData());
    }

    function testFrontMatterParsedWhenEmpty()
    {
        $frontMatter = new FrontMatter("---\n---\nHello World");
        $this->assertSame('Hello World', $frontMatter->getContent());
        $this->assertSame([], $frontMatter->getData());

        $frontMatter = new FrontMatter("---\n---\n\n\nHello World");
        $this->assertSame('Hello World', $frontMatter->getContent());
        $this->assertSame([], $frontMatter->getData());

        $frontMatter = new FrontMatter("---\r\n---\r\nHello World");
        $this->assertSame('Hello World', $frontMatter->getContent());
        $this->assertSame([], $frontMatter->getData());
    }
}
