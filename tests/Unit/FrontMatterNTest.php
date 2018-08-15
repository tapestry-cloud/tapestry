<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Modules\Source\SplFileSource;
use Tapestry\Tests\TestCase;

class FrontMatterNTest extends TestCase
{
    /**
     * Written for issue #148
     * @link https://github.com/carbontwelve/tapestry/issues/148
     */
    function testFrontMatterParsedWhenBodyEmpty()
    {
        try {
            $file = new SplFileSource(new SplFileInfo(__DIR__ . '/../Mocks/TestFileNoBody.md', '', ''));
            $frontMatter = new FrontMatter($file->getRawContent());
            $this->assertSame('', $frontMatter->getContent());
            $this->assertSame([
                'title' => 'Test File Title',
                'draft' => false,
                'date' => 507600000
            ], $frontMatter->getData());
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }

    function testFrontMatterAndBodyParsedCorrectly()
    {
        try {
        $file = new SplFileSource(new SplFileInfo(__DIR__ . '/../Mocks/TestFile.md', '', ''));
        $frontMatter = new FrontMatter($file->getRawContent());
        $this->assertSame('This is a test file...', $frontMatter->getContent());
        $this->assertSame([
            'title' => 'Test File Title',
            'draft' => false,
            'date' => 507600000
        ], $frontMatter->getData());
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
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
