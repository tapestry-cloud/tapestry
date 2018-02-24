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
     * @var array|MutatorInterface[]
     */
    private $mutatorCollection = [];

    /**
     * FilesystemCollector constructor.
     *
     * @param string $sourcePath
     * @param array|MutatorInterface[] $mutatorCollection
     * @throws \Exception
     */
    public function __construct(string $sourcePath, array $mutatorCollection = [])
    {
        if (! file_exists($sourcePath)) {
            throw new \Exception('The source path ['. $sourcePath .'] could not be read or does not exist.');
        }

        $this->sourcePath = $sourcePath;
        $this->mutatorCollection = $mutatorCollection;

        parent::__construct('FilesystemCollector');
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

            // @todo implement isDraft, isIgnored, defaultData, etc as mutators that this iterates over
            foreach($this->mutatorCollection as $mutator) {
                $mutator->mutate($file);
            }

            $collection[$file->getUid()] = $file;
        }

        // @todo implement filters which filter out items from the collection e.g. draft posts...

        return $collection;

        // TODO: Implement collect() method.
    }
}