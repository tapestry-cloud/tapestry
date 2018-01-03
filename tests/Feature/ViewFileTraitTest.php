<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockTapestry;
use Tapestry\Tests\Traits\MockViewFile;

class ViewFileTraitTest extends TestCase
{
    use MockViewFile;
    use MockTapestry;

    public function testIsDraftHelper()
    {
        $this->loadToTmp($this->assetPath('build_test_15/src'));
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_15/check/draft-true.html'),
            $this->tmpPath('build_local/draft-true.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_15/check/draft-false.html'),
            $this->tmpPath('build_local/draft-false.html'),
            '',
            true
        );
    }

    /**
     * Written for issue #161
     * @link https://github.com/carbontwelve/tapestry/issues/161
     */
    public function testExcerptHelper()
    {
        $viewFile = $this->mockViewFile($this->mockTapestry(),__DIR__ . '/../Mocks/TestExcerptFile.md');

        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur&hellip;', $viewFile->getExcerpt());
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur', $viewFile->getExcerpt(50, ''));
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur', $viewFile->getExcerpt(50, null));
        $this->assertEquals('Lorem&hellip;', $viewFile->getExcerpt(10));
        $this->assertEquals('Lorem123', $viewFile->getExcerpt(10, '123'));
        $this->assertEquals('Lorem', $viewFile->getExcerpt(10, ''));
        $this->assertEquals('Lorem', $viewFile->getExcerpt(10, null));

        $viewFile = $this->mockViewFile($this->mockTapestry(),__DIR__ . '/../Mocks/TestFile.md');
        $this->assertEquals('This is a test file...', $viewFile->getExcerpt(50, null));
    }

    public function testGetContentHelper()
    {
        $viewFile = $this->mockViewFile($this->mockTapestry(),__DIR__ . '/../Mocks/TestFile.md');
        $this->assertEquals('This is a test file...', trim($viewFile->getContent()));

        $viewFile->getFile()->setData(['content' => 'TEST test TEST 1234']);
        $this->assertEquals('TEST test TEST 1234', trim($viewFile->getContent()));
    }

    public function testGetPermalinkHelper()
    {
        $viewFile = $this->mockViewFile($this->mockTapestry(),__DIR__ . '/../Mocks/TestExcerptFile.md');
        $this->assertEquals('/testexcerptfile/index.md', $viewFile->getPermalink()); // this has the extension md becuase its not passed through any renderers

        $viewFile->getFile()->setData(['permalink' => '/folder1/folder2/folder3/test.html']);
        $this->assertEquals('/folder1/folder2/folder3/test.html', $viewFile->getPermalink());
    }

    public function testGetUrlHelper()
    {
        $viewFile = $this->mockViewFile(
            $this->mockTapestry(null, ['site' => ['url' => 'http://www.example.com']]),
            __DIR__ . '/../Mocks/TestExcerptFile.md'
        );

        $this->assertEquals('http://www.example.com/testexcerptfile/', $viewFile->getUrl());

        $viewFile->getFile()->setData(['permalink' => '/folder1/folder2/folder3/test.html']);
        $this->assertEquals('http://www.example.com/folder1/folder2/folder3/test.html', $viewFile->getUrl());
    }

    public function testIsPaginatedHelper()
    {
        $viewFile = $this->mockViewFile(
            $this->mockTapestry(null, ['site' => ['url' => 'http://www.example.com']]),
            __DIR__ . '/../Mocks/TestExcerptFile.md'
        );

        $this->assertEquals(false, $viewFile->isPaginated());

        $viewFile->getFile()->setData(['pagination' => 'test']);
        $this->assertEquals(false, $viewFile->isPaginated());

        $viewFile->getFile()->setData(['pagination' => new Pagination(new Project('', '', 'test'))]);
        $this->assertEquals(true, $viewFile->isPaginated());
    }

    public function testHasPreviousNextHelper()
    {
        $viewFile = $this->mockViewFile(
            $this->mockTapestry(null, ['site' => ['url' => 'http://www.example.com']]),
            __DIR__ . '/../Mocks/TestExcerptFile.md'
        );

        $this->assertEquals(false, $viewFile->hasPreviousNext());

        $viewFile->getFile()->setData(['previous_next' => 'test']);
        $this->assertEquals(false, $viewFile->hasPreviousNext());

        $viewFile->getFile()->setData(['previous_next' => new \stdClass()]);
        $this->assertEquals(true, $viewFile->hasPreviousNext());
    }

    public function testGetDateHelper()
    {
        $viewFile = $this->mockViewFile(
            $this->mockTapestry(),
            __DIR__ . '/../Mocks/TestFile.md'
        );

        $this->assertInstanceOf(\DateTime::class, $viewFile->getDate());
        $this->assertEquals('1986', $viewFile->getDate()->format('Y'));
        $this->assertEquals('Feb', $viewFile->getDate()->format('M'));
        $this->assertEquals('01', $viewFile->getDate()->format('d'));
    }
}
