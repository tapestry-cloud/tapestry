<?php

namespace Tapestry\Tests;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Console\Application;
use Tapestry\Console\DefaultInputDefinition;
use Tapestry\Console\Input;
use Tapestry\Tapestry;

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

        // Because the configureIo method of Symfony's command Application pollutes the globals they need resetting
        // between each test.
        putenv('SHELL_VERBOSITY=0');
        unset($_ENV['SHELL_VERBOSITY']);
        unset($_SERVER['SHELL_VERBOSITY']);
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

    /**
     * Load a source folder into tmp.
     *
     * @param string $source
     */
    protected function loadToTmp($source)
    {
        $this->mirror($source, $this->tmpDirectory);
    }

    /**
     * Helper for building tmp paths.
     *
     * @param null|string $path
     * @return string
     */
    protected function tmpPath($path = null) {
        if (is_null($path)) {
            return $this->tmpDirectory;
        }

        if (! in_array(substr($path, 0, 1), ['/', '\\'])) {
            return $this->tmpDirectory . DIRECTORY_SEPARATOR . $path;
        }

        return $this->tmpDirectory . $path;
    }

    /**
     * Helper for building asset paths.
     *
     * @param null|string $path
     * @return string
     */
    protected function assetPath($path = null) {
        if (is_null($path)) {
            return realpath(__DIR__ . '/assets');
        }

        if (! in_array(substr($path, 0, 1), ['/', '\\'])) {
            return realpath(__DIR__ . '/assets') . DIRECTORY_SEPARATOR . $path;
        }

        return realpath(__DIR__ . '/assets') . $path;
    }

    /**
     * Run command line command.
     *
     * @param string $command
     * @param string $argv
     * @param array $options
     * @return ApplicationTester
     */
    protected function runCommand($command, $argv = '', array $options = [])
    {
        $arguments = ['command' => $command];
        $argv = (strlen($argv) > 0) ? explode(' ', $argv) : [];

        foreach ($argv as $value) {
            if (strpos($value, '=') !== false) {
                $tmp = explode('=', $value);
                $arguments[$tmp[0]] = $tmp[1];
                continue;
            }
            $arguments[$value] = true;
        }
        unset($tmp, $value);
        array_unshift($argv, $command);

        $tapestry = new Tapestry(
            new Input(
                $argv,
                new DefaultInputDefinition()
            )
        );

        /** @var Application $cli */
        $cli = $tapestry[Application::class];
        $cli->setAutoExit(false);



        $applicationTester = new ApplicationTester($cli);
        $applicationTester->run($arguments, $options);

        return $applicationTester;
    }

    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @param bool $canonicalize
     * @param bool $ignoreCase
     *
     * @since  Method available since Release 3.2.14
     */
    public static function assertFileEquals(
        $expected,
        $actual,
        $message = '',
        $canonicalize = false,
        $ignoreCase = false
    ) {
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
}