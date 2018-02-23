<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\ProjectFile;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Tests\TestCase;

class FrontMatterNTest extends TestCase
{
    /**
     * Written for issue #148
     * @link https://github.com/carbontwelve/tapestry/issues/148
     */
    function testFrontMatterParsedWhenBodyEmpty()
    {
        $file = new ProjectFile(new SplFileInfo(__DIR__ . '/../Mocks/TestFileNoBody.md', '', ''));
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
        $file = new ProjectFile(new SplFileInfo(__DIR__ . '/../Mocks/TestFile.md', '', ''));
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
