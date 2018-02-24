<?php

namespace Tapestry\Modules\Collectors;

use PDO;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Modules\Source\SourceInterface;

/**
 * Class PDOCollector
 *
 * This collector uses a PDO database connection as the source of Files rather than
 * the file system (as with FilesystemCollector).
 *
 * @package Tapestry\Modules\Collectors
 */
final class PDOCollector extends AbstractCollector implements CollectorInterface
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * PDOCollector constructor.
     *
     * @todo check pdo connection is valid
     * @todo check that tables required by `collect` method exist
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct('PDOCollector');
        $this->pdo = $pdo;
    }

    /**
     * Executes queries on database and returns an array containing
     * all source "files" as instances of MemorySource
     *
     * @return array|SourceInterface[]|MemorySource[]
     */
    public function collect(): array
    {
        // TODO: Implement collect() method.
    }
}