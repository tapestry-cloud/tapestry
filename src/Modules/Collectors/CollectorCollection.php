<?php

namespace Tapestry\Modules\Collectors;

use Tapestry\Modules\Source\SourceInterface;

class CollectorCollection
{
    /**
     * @var array|CollectorInterface[]
     */
    private $collectors = [];

    /**
     * @param CollectorInterface $class
     */
    public function add(CollectorInterface $class)
    {
        $this->collectors[] = $class;
    }

    /**
     * Runs all collectors in collection and merges their output into one array.
     * Because all file id's must be unique it will throw an Exception if there
     * is a clash.
     *
     * @return array|SourceInterface[]
     * @throws \Exception
     */
    public function collect(): array
    {
        $output = [];
        foreach ($this->collectors as $collector) {
            foreach ($collector->collect() as $key => $source) {
                if (isset($output[$key])) {
                    throw new \Exception('File with key ['.$key.'] already collected by previous collector.');
                }
                $output[$key] = $source;
            }
        }

        return $output;
    }
}
