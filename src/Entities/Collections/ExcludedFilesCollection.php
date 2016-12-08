<?php

namespace Tapestry\Entities\Collections;

use Tapestry\ArrayContainer;
use Symfony\Component\Finder\Finder;

class ExcludedFilesCollection extends ArrayContainer
{
    public function excludeFromFinder(Finder $finder)
    {
        $finder->exclude(array_values($this->toArray()));
    }
}
