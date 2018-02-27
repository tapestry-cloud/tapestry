<?php

namespace Tapestry\Modules\Collectors;

use DateTime;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Modules\Collectors\Mutators\MutatorInterface;
use Tapestry\Modules\Source\SourceInterface;
use Tapestry\Modules\Source\SplFileSource;

final class FilesystemCollector extends AbstractCollector implements CollectorInterface
{
    /**
     * The path in which this collector is to go looking for files.
     *
     * @var string
     */
    private $sourcePath;

    /**
     * FilesystemCollector constructor.
     *
     * @param string $sourcePath
     * @param array|MutatorInterface[] $mutatorCollection
     * @param array $filterCollection
     * @throws \Exception
     */
    public function __construct(string $sourcePath, array $mutatorCollection = [], array $filterCollection = [])
    {
        if (! file_exists($sourcePath)) {
            throw new \Exception('The source path ['. $sourcePath .'] could not be read or does not exist.');
        }

        $this->sourcePath = $sourcePath;

        parent::__construct('FilesystemCollector', $mutatorCollection, $filterCollection);
    }

    /**
     * Traverses source folder and returns an array containing
     * all source files as instances of SplFileSource.
     *
     * @return array|SourceInterface[]|SplFileSource[]
     * @throws \Exception
     */
    public function collect(): array
    {
        $collection = [];

        $finder = new Finder();
        $finder->files()
            ->followLinks()
            ->in($this->sourcePath)
            ->ignoreDotFiles(true);

        /** @var SplFileInfo $file */
        foreach ($finder->files() as $file) {
            $file = new SplFileSource($file, [
                'draft' => false,
                'date' => DateTime::createFromFormat('U', $file->getMTime()),
                'pretty_permalink' => true
            ]);
            $collection[$file->getUid()] = $this->mutateSource($file);
        }

        // @todo implement filters which filter out items from the collection e.g. draft posts...

        return $collection;

        // TODO: Implement collect() method.
    }
}