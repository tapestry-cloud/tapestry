<?php

namespace Tapestry\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Entities\ViewFile;
use Tapestry\Modules\Content\FrontMatter;

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

    /**
     * Written for issue #161
     * @link https://github.com/carbontwelve/tapestry/issues/161
     */
    public function testExtractHelper()
    {
        $project = new Project('', '', 'test');

        $file = new File(new SplFileInfo(__DIR__ . '/mocks/TestExcerptFile.md', '', ''));
        $frontMatter = new FrontMatter($file->getFileContent());
        $file->setData($frontMatter->getData());
        $file->setContent($frontMatter->getContent());

        $project->set('compiled', [
            $file->getUid() => $file,
        ]);

        $viewFile = new ViewFile($project, $file->getUid());

        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur&hellip;', $viewFile->getExcerpt());
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur', $viewFile->getExcerpt(50, ''));
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur', $viewFile->getExcerpt(50, null));
        $this->assertEquals('Lorem&hellip;', $viewFile->getExcerpt(10));
        $this->assertEquals('Lorem123', $viewFile->getExcerpt(10, '123'));
        $this->assertEquals('Lorem', $viewFile->getExcerpt(10, ''));
        $this->assertEquals('Lorem', $viewFile->getExcerpt(10, null));
    }
}
