<?php

namespace Tapestry\Modules\Collectors\Mutators;

use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Modules\Source\SourceInterface;

/**
 * Class FrontMatterMutator.
 */
class FrontMatterMutator implements MutatorInterface
{
    public function mutate(SourceInterface &$source)
    {
        $parser = new FrontMatter($source->getRawContent());

        $source->setRenderedContent($parser->getContent());
        $source->setDataFromArray($parser->getData());
    }
}
