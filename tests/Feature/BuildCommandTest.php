<?php

namespace Tapestry\Tests\Feature;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Tests\TestCase;

class BuildCommandTest extends TestCase
{
    /**
     * Test that we are within the right path for Jigsaw to be tested.
     */
    public function testCurrentWorkingDirectoryIsTestTemp()
    {
        $this->assertEquals($this->tmpPath(), getcwd());
    }

    public function testDefaultInit()
    {
        $this->loadToTmp($this->assetPath('build_test_1/src'));

        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileExists($this->tmpPath('build_local'));
        $this->assertFileExists($this->tmpPath('build_local'.DIRECTORY_SEPARATOR.'index.html'));
        $this->assertFileExists($this->tmpPath('build_local'.DIRECTORY_SEPARATOR.'about.html'));
        $this->assertFileExists($this->tmpPath('build_local'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'app.js'));

        $this->assertFileEquals(
            $this->assetPath('build_test_1/check/about.html'),
            $this->tmpPath('/build_local/about.html'),
            '',
            true
        );
        $this->assertFileEquals(
            $this->assetPath('build_test_1/check/index.html'),
            $this->tmpPath('build_local/index.html'),
            '',
            true
        );
    }

    public function testMarkdownFrontMatterParsedOut()
    {
        $this->loadToTmp($this->assetPath('build_test_2/src'));

        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_2/check/about.html'),
            $this->tmpPath('build_local/about.html'),
            '',
            true
        );
    }

    public function testPrettyPermalinksParsed()
    {
        $this->loadToTmp($this->assetPath('build_test_3/src'));

        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_3/check/index.html'),
            $this->tmpPath('build_local/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_3/check/about.html'),
            $this->tmpPath('build_local/about/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_3/check/not-pretty.html'),
            $this->tmpPath('build_local/not-pretty.html'),
            '',
            true
        );
    }

    public function testSiteDistOption()
    {
        $this->loadToTmp($this->assetPath('build_test_3/src'));
        $output = $this->runCommand('build', '--quiet --dist-dir=' . $this->tmpPath('test_dist_dir'));

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileExists($this->tmpPath('test_dist_dir/index.html'));
        $this->assertFileExists($this->tmpPath('test_dist_dir/about/index.html'));
        $this->assertFileExists($this->tmpPath('test_dist_dir/not-pretty.html'));
    }

    /**
     * Written for issue 89
     * @link https://github.com/carbontwelve/tapestry/issues/89
     */
    public function testFrontMatterDataParsingSucceeds()
    {
        $this->loadToTmp($this->assetPath('build_test_24/src'));
        $output = $this->runCommand('build', '--quiet --dist-dir=' . $this->tmpPath('test_dist_dir'));

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());
    }

    /**
     * Written for issue 89
     * @link https://github.com/carbontwelve/tapestry/issues/89
     */
    public function testFrontMatterDataParsingFails()
    {
        $this->loadToTmp($this->assetPath('build_test_25/src'));
        $output = $this->runCommand('build', '--quiet --dist-dir=' . $this->tmpPath('test_dist_dir'));
        $this->assertContains('[!] The date [abc] is in a format not supported by Tapestry', trim($output->getDisplay()));
        $this->assertEquals(1, $output->getStatusCode());
    }

    /**
     * Written for issue 121
     * @link https://github.com/carbontwelve/tapestry/issues/121
     */
    public function testFilterFunctionality()
    {
        $this->loadToTmp($this->assetPath('build_test_4/src'));

        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        // Folders prefixed with a underscore should be ignored by default.
        $this->assertFileNotExists($this->tmpPath('build_local/_templates'));

        // Folders set to be ignored, should be ignored.
        $this->assertFileNotExists($this->tmpPath('build_local/ignored_folder'));

        // Unless they are set to be copied.
        $this->assertfileExists($this->tmpPath('build_local/assets'));
        $this->assertFileEquals($this->assetPath('build_test_4/src/source/assets/js/app.js'), $this->tmpPath('build_local/assets/js/app.js'));
        $this->assertFileEquals($this->assetPath('build_test_4/src/source/assets/js/something_else/a.js'), $this->tmpPath('build_local/assets/js/something_else/a.js'));
        $this->assertFileEquals($this->assetPath('build_test_4/src/source/assets/js/something_else/b.js'), $this->tmpPath('build_local/assets/js/something_else/b.js'));
    }

    /**
     * Written for issue #123
     * @link https://github.com/carbontwelve/tapestry/issues/123
     */
    public function testPHPAsPHTML()
    {
        $this->loadToTmp($this->assetPath('build_test_29/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_29/check/index.html'),
            $this->tmpPath('build_local/index.html'),
            '',
            true
        );
    }

    /**
     * Written for issue #130
     * Originally from a previous incarnation of Tapestry this tests that input files produce the correct output paths.
     *
     * @link https://github.com/carbontwelve/tapestry/issues/130
     */
    public function testComplexBaseBuild()
    {
        $this->loadToTmp($this->assetPath('build_test_5/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileExists($this->tmpPath('build_local/a_folder/b_folder/b-file/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/a_folder/a-file/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/a_folder/another-file/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/b_folder/b-file-2/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/b_folder/b-file-3.html'));
        $this->assertFileExists($this->tmpPath('build_local/about/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/index.html'));
    }

    /**
     * Written for issue #131
     * @link https://github.com/carbontwelve/tapestry/issues/131
     */
    public function testFrontMatterTemplateLoading()
    {
        $this->loadToTmp($this->assetPath('build_test_6/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals($this->assetPath('build_test_6/check/index.html'), $this->tmpPath('build_local/index.html'));
    }

    /**
     * Written for issue #132
     * @link https://github.com/carbontwelve/tapestry/issues/132
     */
    public function testFontMatterPermainks()
    {
        $this->loadToTmp($this->assetPath('build_test_7/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileExists($this->tmpPath('build_local/abc/123/file.html'));
        $this->assertFileExists($this->tmpPath('build_local/123/abc/file.xml'));
        $this->assertFileExists($this->tmpPath('build_local/rah.html'));
        $this->assertFileExists($this->tmpPath('build_local/about/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/test/testing/testy/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/blog/2016/02/test.html'));
    }

    /**
     * Written for issue #136
     * @link https://github.com/carbontwelve/tapestry/issues/136
     */
    public function testBlogPostBuild()
    {
        $this->loadToTmp($this->assetPath('build_test_11/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_11/check/blog/2016/test-blog-entry.html'),
            $this->tmpPath('build_local/blog/2016/test-blog-entry/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_11/check/blog/2016/test-blog-entry-two.html'),
            $this->tmpPath('build_local/blog/2016/test-blog-entry-two/index.html'),
            '',
            true
        );
    }

    /**
     * Written for issue #152
     * @link https://github.com/carbontwelve/tapestry/issues/152
     */
    public function testIgnoreUnderscorePaths()
    {
        $this->loadToTmp($this->assetPath('build_test_30/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileNotExists($this->tmpPath('build_local/_should-not-exist/index.html'));
        $this->assertFileExists($this->tmpPath('build_local/should-exist/index.html'));
        $this->assertFileNotExists($this->tmpPath('build_local/should-exist/_should-not-exist/index.html'));
    }

    /**
     * Written for issue #158
     * @link https://github.com/carbontwelve/tapestry/issues/158
     */
    public function testFileTemplatePassThrough()
    {
        $this->loadToTmp($this->assetPath('build_test_31/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_31/check/single.html'),
            $this->tmpPath('build_local/single/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_31/check/base.html'),
            $this->tmpPath('build_local/base/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_31/check/blog.html'),
            $this->tmpPath('build_local/blog/2016/test/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_31/check/page.html'),
            $this->tmpPath('build_local/page/index.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_31/check/page-multi.html'),
            $this->tmpPath('build_local/page-multi/index.html'),
            '',
            true
        );
    }

    /**
     * Written for issue #208
     * @link https://github.com/carbontwelve/tapestry/issues/208
     */
    public function testDoubleDotFileExt()
    {
        $this->loadToTmp($this->assetPath('build_test_35/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileNotExists($this->tmpPath('build_local/css/main-min.css'));
        $this->assertFileExists($this->tmpPath('build_local/css/main.min.css'));

        $this->assertFileNotExists($this->tmpPath('build_local/abc-123-xyz.html'));
        $this->assertFileExists($this->tmpPath('build_local/abc.123.xyz.html'));
    }

    /**
     * Written for issue #156
     * @link https://github.com/carbontwelve/tapestry/issues/156
     */
    public function testPermalinkClashes()
    {
        $this->loadToTmp($this->assetPath('build_test_36/src'));
        $output = $this->runCommand('build', '', ['verbosity' => OutputInterface::VERBOSITY_NORMAL]);

        $this->assertTrue(strpos(trim($output->getDisplay()), 'The permalink [/file-clash.html] is already in use!') !== false);
        $this->assertEquals(1, $output->getStatusCode());
    }

    /**
     * Written for issue #255
     * @link https://github.com/tapestry-cloud/tapestry/issues/255
     */
    public function testPermalinkClashesOnStatic()
    {
        $this->loadToTmp($this->assetPath('build_test_39/src'));
        $output = $this->runCommand('build', '');

        $this->assertEquals(0, $output->getStatusCode());
    }
}
