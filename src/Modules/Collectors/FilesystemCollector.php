<?php

namespace Tapestry\Modules\Collectors;

use Tapestry\Modules\Source\SourceInterface;
use Tapestry\Modules\Source\SplFileSource;

final class FilesystemCollector extends AbstractCollector implements CollectorInterface
{
    /**
     * FilesystemCollector constructor.
     */
    public function __construct()
    {
        parent::__construct('FilesystemCollector');
    }

    /**
     * Traverses source folder and returns an array containing
     * all source files as instances of SplFileSource.
     *
     * @return array|SourceInterface[]|SplFileSource[]
     */
    public function collect(): array
    {
        // TODO: Implement collect() method.
    }
}