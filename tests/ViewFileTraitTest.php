<?php

namespace Tapestry\Tests;

use Carbon\Carbon;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\File;
use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Entities\ViewFile;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Tapestry;

class ViewFileTraitTest extends CommandTestBase
{
    public function testIsDraftHelper()
    {
        $this->copyDirectory('assets/build_test_15/src', '_tmp');
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_15/check/draft-true.html',
            __DIR__.'/_tmp/build_local/draft-true.html',
            '',
            true
        );

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_15/check/draft-false.html',
            __DIR__.'/_tmp/build_local/draft-false.html',
            '',
            true
        );
    }

    private function mockViewFile($viewPath, $configuration = null) {
        if (is_array($configuration)) {
            Tapestry::getInstance()->getContainer()->add(Configuration::class, new Configuration($configuration));
        }
        $project = new Project('', '', 'test');

        $file = new File(new SplFileInfo($viewPath, '', ''));
        $frontMatter = new FrontMatter($file->getFileContent());
        $file->setData($frontMatter->getData());
        $file->setContent($frontMatter->getContent());

        $project->set('compiled', [
            $file->getUid() => $file,
        ]);

        return new ViewFile($project, $file->getUid());
    }

    /**
     * Written for issue #161
     * @link https://github.com/carbontwelve/tapestry/issues/161
     */
    public function testExcerptHelper()
    {
        $viewFile = $this->mockViewFile(__DIR__ . '/Mocks/TestExcerptFile.md');

        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur&hellip;', $viewFile->getExcerpt());
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur', $viewFile->getExcerpt(50, ''));
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur', $viewFile->getExcerpt(50, null));
        $this->assertEquals('Lorem&hellip;', $viewFile->getExcerpt(10));
        $this->assertEquals('Lorem123', $viewFile->getExcerpt(10, '123'));
        $this->assertEquals('Lorem', $viewFile->getExcerpt(10, ''));
        $this->assertEquals('Lorem', $viewFile->getExcerpt(10, null));

        $viewFile = $this->mockViewFile(__DIR__ . '/Mocks/TestFile.md');
        $this->assertEquals('This is a test file...', $viewFile->getExcerpt(50, null));
    }

    public function testGetContentHelper()
    {
        $viewFile = $this->mockViewFile(__DIR__ . '/Mocks/TestFile.md');
        $this->assertEquals('This is a test file...', trim($viewFile->getContent()));

        $viewFile->getFile()->setData(['content' => 'TEST test TEST 1234']);
        $this->assertEquals('TEST test TEST 1234', trim($viewFile->getContent()));
    }

    public function testGetPermalinkHelper()
    {
        $viewFile = $this->mockViewFile(__DIR__ . '/Mocks/TestExcerptFile.md');
        $this->assertEquals('/testexcerptfile/index.md', $viewFile->getPermalink()); // this has the extension md becuase its not passed through any renderers

        $viewFile->getFile()->setData(['permalink' => '/folder1/folder2/folder3/test.html']);
        $this->assertEquals('/folder1/folder2/folder3/test.html', $viewFile->getPermalink());
    }

    public function testGetUrlHelper()
    {
        $viewFile = $this->mockViewFile(
            __DIR__ . '/Mocks/TestExcerptFile.md', ['site' => ['url' => 'http://www.example.com']]
        );

        $this->assertEquals('http://www.example.com/testexcerptfile/', $viewFile->getUrl());

        $viewFile->getFile()->setData(['permalink' => '/folder1/folder2/folder3/test.html']);
        $this->assertEquals('http://www.example.com/folder1/folder2/folder3/test.html', $viewFile->getUrl());
    }

    public function testIsPaginatedHelper()
    {
        $viewFile = $this->mockViewFile(
            __DIR__ . '/Mocks/TestExcerptFile.md', ['site' => ['url' => 'http://www.example.com']]
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
            __DIR__ . '/Mocks/TestExcerptFile.md', ['site' => ['url' => 'http://www.example.com']]
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
            __DIR__ . '/Mocks/TestFile.md'
        );

        $this->assertInstanceOf(\DateTime::class, $viewFile->getDate());
        $this->assertEquals('1986', $viewFile->getDate()->format('Y'));
        $this->assertEquals('Feb', $viewFile->getDate()->format('M'));
        $this->assertEquals('01', $viewFile->getDate()->format('d'));
    }
}
