<?php

namespace Tapestry\Modules\Collectors;

use DateTime;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Modules\Source\SourceInterface;

/**
 * Class MemoryCollector
 * @package Tapestry\Modules\Collectors
 */
final class MemoryCollector extends AbstractCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private $items;

    /**
     * MemoryCollector constructor.
     * @param array $items
     * @param array $mutatorCollection
     * @param array $filterCollection
     */
    public function __construct(array $items = [], array $mutatorCollection = [], array $filterCollection = [])
    {
        parent::__construct('MemoryCollector', $mutatorCollection, $filterCollection);
        $this->items = $items;
    }

    /**
     * @return array|SourceInterface[]|MemorySource[]
     * @throws \Exception
     */
    public function collect(): array
    {
        $collection = [];

        foreach($this->items as $item)
        {
            $file = new MemorySource(
                $item['uid'],
                $item['rawContent'],
                $item['filename'],
                $item['ext'],
                $item['relativePath'],
                $item['relativePathname'],
                $item['data'] ?? [
                    'draft' => false,
                    'date' => DateTime::createFromFormat('U', time()),
                    'pretty_permalink' => true
                ]
            );

            $collection[$file->getUid()] = $file;
        }

        return $this->filterCollection($collection);
    }
}