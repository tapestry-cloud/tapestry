<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Profiler;
use Tapestry\Tests\TestCase;

class StopwatchProfilerTest extends TestCase
{
    public function testProfiler()
    {
        $profiler = new Profiler();

        $profiler->addItem('BootKernel_START', 100000, 100, 100);
        $profiler->addItem('BootKernel_FINISH', 100100, 200, 250);

        $profiler->addItem('ReadCache_START', 100110, 200, 250);
        $profiler->addItem('ReadCache_FINISH', 100250, 220, 250);

        $profiler->addItem('Compile_START', 100250, 220, 250);

        $profiler->addItem('Compile.SubTaskA_START', 100270, 220, 250);
        $profiler->addItem('Compile.SubTaskA_FINISH', 100570, 350, 450);

        $profiler->addItem('Compile.SubTaskB_START', 100570, 350, 450);
        $profiler->addItem('Compile.SubTaskB_FINISH', 100970, 650, 650);

        $profiler->addItem('Compile_FINISH', 101250, 720, 850);

        $report = $profiler->report();

        $this->assertTrue(key_exists('BootKernel', $report));
        $this->assertEquals(100, $report['BootKernel']['execution_time']);
        $this->assertEquals(100, $report['BootKernel']['memory_consumption']);

        $this->assertTrue(key_exists('ReadCache', $report));
        $this->assertEquals(140, $report['ReadCache']['execution_time']);
        $this->assertEquals(20, $report['ReadCache']['memory_consumption']);

        $this->assertTrue(key_exists('Compile', $report));
        $this->assertEquals(1000, $report['Compile']['execution_time']);
        $this->assertEquals(500, $report['Compile']['memory_consumption']);

        $this->assertTrue(key_exists('Compile.SubTaskA', $report));
        $this->assertEquals(300, $report['Compile.SubTaskA']['execution_time']);
        $this->assertEquals(130, $report['Compile.SubTaskA']['memory_consumption']);

        $this->assertTrue(key_exists('Compile.SubTaskB', $report));
        $this->assertEquals(400, $report['Compile.SubTaskB']['execution_time']);
        $this->assertEquals(300, $report['Compile.SubTaskB']['memory_consumption']);
    }
}