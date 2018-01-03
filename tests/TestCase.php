<?php

namespace Tapestry\Tests;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;

class TestCase extends \PHPUnit_Framework_TestCase {

    /**
     * The unique tmp test directory for this test case, set on each test setup.
     *
     * @var string
     */
    protected $tmpDirectory;

    /**
     * Called before each test is executed.
     */
    protected function setUp()
    {
        $this->tmpDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . '_tapestry_tmp_' . sha1(microtime());
        while(file_exists($this->tmpDirectory)){
            $this->tmpDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . '_tapestry_tmp_' . sha1(microtime());
        }
        mkdir($this->tmpDirectory);
        chdir($this->tmpDirectory);
    }

    /**
     * Called after each test is executed.
     */
    protected function tearDown()
    {
        $it = new RecursiveDirectoryIterator($this->tmpDirectory, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        chdir(__DIR__);
        rmdir($this->tmpDirectory);
    }

    /**
     * Copy a file.
     *
     * @param string $origin
     * @param string $destination
     * @param bool $overwrite
     * @return void
     */
    protected function copy($origin, $destination, $overwrite = false)
    {
        (new Filesystem())->copy($origin, $destination, $overwrite);
    }

    /**
     * Mirror $origin to $destination.
     *
     * @param string $origin
     * @param string $destination
     * @return void
     */
    protected function mirror($origin, $destination)
    {
        (new Filesystem())->mirror($origin, $destination);
    }

    protected function loadToTmp($source)
    {
        $this->mirror($source, $this->tmpDirectory);
    }
}