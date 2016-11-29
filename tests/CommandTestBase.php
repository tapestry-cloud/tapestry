<?php namespace Tapestry\Tests;

use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Console\Application;

abstract class CommandTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var null|Application
     */
    protected $cli;

    /**
     * @var string
     */
    protected static $tmpPath;

    /**
     * @var Filesystem
     */
    protected static $fileSystem;

    /**
     * Before the test cases are run, change directory to the tests directory and set the _tmp path
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$tmpPath = __DIR__ . DIRECTORY_SEPARATOR . '_tmp';
        $fileSystem = new Filesystem();
        $fileSystem->mkdir(self::$tmpPath);
        chdir(self::$tmpPath);
        self::$fileSystem  = $fileSystem;
    }

    public static function tearDownAfterClass()
    {
        //self::$fileSystem->remove(self::$tmpPath);
    }

    /**
     * Clean the _tmp path between tests so they do not conflict with one another
     */
    protected function tearDown()
    {
        $directoryContent = new \RecursiveDirectoryIterator(self::$tmpPath, \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($directoryContent, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            $file->isDir() ?  rmdir($file) : unlink($file);
        }
    }

    /**
     * @return Application
     */
    private function createCliApplication()
    {
        /** @var Application $app */
        $app = require __DIR__ . '/../src/bootstrap.php';

        //$app->reboot();

        /** @var Application $cli */
        $cli = $app[Application::class];
        $cli->setAutoExit(false);
        return $cli;
    }

    protected function copyDirectory($from, $to)
    {
        $from = __DIR__ . DIRECTORY_SEPARATOR . $from;
        $to = __DIR__ . DIRECTORY_SEPARATOR . $to;
        $directoryContent = new \RecursiveDirectoryIterator($from, \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($directoryContent, \RecursiveIteratorIterator::CHILD_FIRST);
        /** @var \SplFileInfo $item */
        foreach($files as $item) {
            if ($item->isDir()){
                self::$fileSystem->mkdir(str_replace($from, $to, $item->getPath()));
            }else{
                self::$fileSystem->copy($item->getPathname(), str_replace($from, $to, $item->getPathname()));
            }
        }
    }
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     *
     * @since  Method available since Release 3.2.14
     */
    public static function assertFileEquals($expected, $actual, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        self::assertFileExists($expected, $message);
        self::assertFileExists($actual, $message);
        self::assertEquals(
            preg_replace('~\R~u', "\r\n", trim(file_get_contents($expected))),
            preg_replace('~\R~u', "\r\n", trim(file_get_contents($actual))),
            $message,
            0,
            10,
            $canonicalize,
            $ignoreCase
        );
    }
    /**
     * Using the cli application itself, execute a command that already exists
     *
     * @param string $command
     * @param array $arguments
     * @return ApplicationTester
     */
    protected function runCommand($command, array $arguments = [])
    {
        $applicationTester = new ApplicationTester($this->getCli());
        $arguments = array_merge(['command' => $command], $arguments);
        $applicationTester->run($arguments);
        return $applicationTester;
    }
    /**
     * Obtain the cli application for testing
     * @return Application
     */
    private function getCli()
    {
        if (is_null($this->cli)) {
            $this->cli = $this->createCliApplication();
        }
        return $this->cli;
    }

}