<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;

class LockFileTest extends CommandTestBase
{
    /**
     * Written for issue #157
     * @link https://github.com/carbontwelve/tapestry/issues/157
     */
    public function testLockFileKillsTask()
    {
        $this->copyDirectory('assets/build_test_40/src', '_tmp');
        $lock = fopen(__DIR__ . DIRECTORY_SEPARATOR . '_tmp' . DIRECTORY_SEPARATOR . '.lock', 'w+');
        $this->assertTrue(flock($lock, LOCK_EX | LOCK_NB));

        $output = $this->runCommand('build', '');
        $this->assertEquals(1, $output->getStatusCode());
    }

    /**
     * Written for issue #157
     * @link https://github.com/carbontwelve/tapestry/issues/157
     */
    public function testIgnoringLockFile()
    {
        $this->copyDirectory('assets/build_test_40/src', '_tmp');
        $output = $this->runCommand('build', '--no-lock');
        $this->assertEquals(0, $output->getStatusCode());
    }
}
