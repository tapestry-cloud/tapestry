<?php

namespace Tapestry\Modules\Source;

use Tapestry\Entities\DependencyGraph\Node;

/**
 * Class ClonedSource
 *
 * This class is used by Generators in order to duplicate a source file when needed.
 * For example a template uses the pagination generator which makes a clone of the
 * template once for each page of the pagination and injects into each just the
 * items for that page.
 *
 */
class ClonedSource extends MemorySource
{

    /**
     * ClonedSource constructor.
     *
     * @param SourceInterface|Node $source
     * @throws \Exception
     */
    public function __construct(SourceInterface $source)
    {
        parent::__construct(
            $source->getUid(),
            $source->getRawContent(),
            $source->getFilename(),
            $source->getExtension(),
            $source->getRelativePath(),
            $source->getRelativePathname(),
            $source->getData()
        );

        $this->setCloned();
    }
}