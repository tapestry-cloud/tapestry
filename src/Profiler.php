<?php

namespace Tapestry;

/**
 * Class Profiler
 * @package Tapestry
 *
 * This deals with functionality related to the --stopwatch flag
 */
class Profiler
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param $name
     * @param null|int|float $time
     * @param null|int $memoryUse
     * @param null|int $memoryPeak
     */
    public function addItem($name, $time = null, $memoryUse = null, $memoryPeak = null)
    {
        array_push($this->items, [
            'name' => $name,
            'time' => is_null($time) ? microtime(true) : $time,
            'memory_use' => is_null($memoryUse) ? memory_get_usage(true) : $memoryUse,
            'memory_peak' => is_null($memoryPeak) ? memory_get_peak_usage(true) : $memoryPeak,
        ]);
    }

    public function report()
    {
        $report = [];
        foreach ($this->items as $item) {
            $name = explode('_', $item['name']);
            $status = $name[1];
            $name = $name[0];

            if (! isset($report[$name])) {
                $report[$name] = [];
            }

            $report[$name][$status.'_time'] = $item['time'];
            $report[$name][$status.'_memory_use'] = $item['memory_use'];
            $report[$name][$status.'_memory_peak'] = $item['memory_peak'];

            // If a start & finish time are available, then work out the stats
            if (isset($report[$name]['START_time']) && isset($report[$name]['FINISH_time'])) {
                $report[$name]['execution_time'] = round(($report[$name]['FINISH_time'] - $report[$name]['START_time']),3);
                $report[$name]['memory_consumption'] = $report[$name]['FINISH_memory_use'] - $report[$name]['START_memory_use'];
                $report[$name]['memory_use'] = $report[$name]['FINISH_memory_use'];
                $report[$name]['memory_peak'] = ($report[$name]['START_memory_peak'] < $report[$name]['FINISH_memory_peak']) ? $report[$name]['FINISH_memory_peak'] : $report[$name]['START_memory_peak'];
            }
        }

        // Filter out any clocks that have no start or finish time, this should probably throw an exception in debug mode?
        $report = array_filter($report, function($value) {
            return isset($value['START_time']) && isset($value['FINISH_time']);
        });

        return $report;
    }
}