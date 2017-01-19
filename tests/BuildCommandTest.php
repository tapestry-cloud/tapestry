<?php

namespace Tapestry\Tests;

class BuildCommandTest extends CommandTestBase
{
    /**
     * Test that we are within the right path for Jigsaw to be tested.
     */
    public function testCurrentWorkingDirectoryIsTestTemp()
    {
        $this->assertEquals(self::$tmpPath, getcwd());
    }

    public function testDefaultInit()
    {
        $this->copyDirectory('/assets/build_test_1/src', '/_tmp');

        $output = $this->runCommand('build', ['--quiet']);

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileExists(self::$tmpPath.DIRECTORY_SEPARATOR.'build_local');
        $this->assertFileExists(self::$tmpPath.DIRECTORY_SEPARATOR.'build_local'.DIRECTORY_SEPARATOR.'index.html');
        $this->assertFileExists(self::$tmpPath.DIRECTORY_SEPARATOR.'build_local'.DIRECTORY_SEPARATOR.'about.html');
        $this->assertFileExists(self::$tmpPath.DIRECTORY_SEPARATOR.'build_local'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'app.js');

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_1/check/about.html',
            __DIR__.'/_tmp/build_local/about.html',
            '',
            true
        );
        $this->assertFileEquals(
            __DIR__.'/assets/build_test_1/check/index.html',
            __DIR__.'/_tmp/build_local/index.html',
            '',
            true
        );
    }

    public function testMarkdownFrontmatterParsedOut()
    {
        $this->copyDirectory('assets/build_test_2/src', '_tmp');

        $output = $this->runCommand('build', ['--quiet']);

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_2/check/about.html',
            __DIR__.'/_tmp/build_local/about.html',
            '',
            true
        );
    }

    public function testPrettyPermalinksParsed()
    {
        $this->copyDirectory('assets/build_test_3/src', '_tmp');

        $output = $this->runCommand('build', ['--quiet']);

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_3/check/index.html',
            __DIR__.'/_tmp/build_local/index.html',
            '',
            true
        );

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_3/check/about.html',
            __DIR__.'/_tmp/build_local/about/index.html',
            '',
            true
        );

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_3/check/not-pretty.html',
            __DIR__.'/_tmp/build_local/not-pretty.html',
            '',
            true
        );
    }

    public function testSiteDistOption()
    {
        $this->copyDirectory('assets/build_test_3/src', '_tmp');
        $output = $this->runCommand('build', ['--quiet', '--dist-dir' => __DIR__ . '/_tmp/test_dist_dir']);

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileExists(__DIR__.'/_tmp/test_dist_dir/index.html');
        $this->assertFileExists(__DIR__.'/_tmp/test_dist_dir/about/index.html');
        $this->assertFileExists(__DIR__.'/_tmp/test_dist_dir/not-pretty.html');
    }

//
    //public function testFilterFunctionality()
    //{
    //    $this->copyDirectory('assets/build_test_4/src', '_tmp');
//
    //    $output = $this->runCommand('build');
//
    //    $this->assertEquals('Site successfully built.', trim($output->getDisplay()));
    //    $this->assertEquals(0, $output->getStatusCode());
//
    //    $this->assertFileNotExists(__DIR__ . '/_tmp/build_local/_templates');
    //    $this->assertFileNotExists(__DIR__ . '/_tmp/build_local/_ignored_folder');
    //    $this->assertfileExists(__DIR__ . '/_tmp/build_local/assets');
    //    $this->assertFileEquals(__DIR__ .'/assets/build_test_4/src/source/assets/js/app.js', __DIR__ . '/_tmp/build_local/assets/js/app.js');
    //    $this->assertFileEquals(__DIR__ .'/assets/build_test_4/src/source/assets/js/something_else/a.js', __DIR__ . '/_tmp/build_local/assets/js/something_else/a.js');
    //    $this->assertFileEquals(__DIR__ .'/assets/build_test_4/src/source/assets/js/something_else/b.js', __DIR__ . '/_tmp/build_local/assets/js/something_else/b.js');
    //}
//
    //public function testComplexBaseBuild()
    //{
    //    $this->copyDirectory('assets/build_test_5/src', '_tmp');
//
    //    $output = $this->runCommand('build');
//
    //    $this->assertEquals('Site successfully built.', trim($output->getDisplay()));
    //    $this->assertEquals(0, $output->getStatusCode());
//
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/a_folder/b_folder/b_file/index.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/a_folder/a_file/index.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/a_folder/another_file/index.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/b_folder/b_file_2/index.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/b_folder/b_file_3.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/about/index.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/index.html');
    //}
//
    //public function testFrontmatterTemplateLoading()
    //{
    //    $this->copyDirectory('assets/build_test_6/src', '_tmp');
//
    //    $output = $this->runCommand('build');
//
    //    $this->assertEquals('Site successfully built.', trim($output->getDisplay()));
    //    $this->assertEquals(0, $output->getStatusCode());
//
    //    $this->assertFileEquals(__DIR__ .'/assets/build_test_6/check/index.html', __DIR__ . '/_tmp/build_local/index.html');
    //}
//
    //public function testFontmatterPermalinks()
    //{
    //    $this->copyDirectory('assets/build_test_7/src', '_tmp');
//
    //    $output = $this->runCommand('build');
//
    //    $this->assertEquals('Site successfully built.', trim($output->getDisplay()));
    //    $this->assertEquals(0, $output->getStatusCode());
//
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/abc/123/file.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/123/abc/file.xml');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/rah.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/about/index.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/test/testing/testy/index.html');
    //    $this->assertFileExists(__DIR__ . '/_tmp/build_local/blog/2016/02/test.html');
    //}
}
