<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\TestCase;

class LockFileTest extends TestCase
{
    /**
     * Written for issue #157
     * @link https://github.com/carbontwelve/tapestry/issues/157
     */
    public function testLockFileKillsTask()
    {
        $this->loadToTmp($this->assetPath('build_test_40/src'));
        $lock = fopen($this->tmpPath('.lock'), 'w+');
        $this->assertTrue(flock($lock, LOCK_EX | LOCK_NB));

        $output = $this->runCommand('build', '');
        $this->assertEquals(1, $output->getStatusCode());
        fclose($lock);
    }

    /**
     * Written for issue #157
     * @link https://github.com/carbontwelve/tapestry/issues/157
     */
    public function testIgnoringLockFile()
    {
        $this->loadToTmp($this->assetPath('build_test_40/src'));
        touch($this->tmpPath('.lock'));
        $output = $this->runCommand('build', '--no-lock');
        $this->assertEquals(0, $output->getStatusCode());
    }
}
