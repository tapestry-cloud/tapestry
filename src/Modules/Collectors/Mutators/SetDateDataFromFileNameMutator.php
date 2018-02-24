<?php

namespace Tapestry\Modules\Collectors\Mutators;

use DateTime;
use Tapestry\Modules\Source\SourceInterface;

final class SetDateDataFromFileNameMutator implements MutatorInterface
{
    /**
     * If the source basename matches YYYY-MM-DD-title-as-a-slug then it's used to
     * set the source's date, slug and title data attributes.
     *
     * @param SourceInterface $source
     */
    public function mutate(SourceInterface &$source)
    {
        preg_match('/^(\d{4}-\d{2}-\d{2})-(.*)/', $source->getBasename(),$matches);
        if (count($matches) === 3) {
            $source->setDataFromArray([
                'date' => new DateTime($matches[1]),
                'slug' => $matches[2],
                'title' => ucfirst(str_replace('-', ' ', $matches[2]))
            ]);
            return;
        }

        preg_match('/^(\d{2}-\d{2}-\d{4})-(.*)/', $source->getBasename(),$matches);
        if (count($matches) === 3) {
            $source->setDataFromArray([
                'date' => new DateTime($matches[1]),
                'slug' => $matches[2],
                'title' => ucfirst(str_replace('-', ' ', $matches[2]))
            ]);
        }
    }
}