<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Modules\Collectors\FilesystemCollector;
use Tapestry\Tests\TestCase;

class FilesystemCollectorNTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testExceptionOnInvalidPath()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The source path [does-not-exist] could not be read or does not exist.');

        new FilesystemCollector('does-not-exist');
    }

    public function testFilesystemCollector()
    {

        $this->loadToTmp($this->assetPath('build_test_41/src'));

        try {
            $class = new FilesystemCollector($this->assetPath('build_test_41/src/source'));
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }

        $arr = $class->collect();
        $this->assertTrue(is_array($arr));

    }

}