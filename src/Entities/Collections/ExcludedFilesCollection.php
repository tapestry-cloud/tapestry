<?php

namespace Tapestry\Entities\Collections;

use Symfony\Component\Finder\Finder;
use Tapestry\ArrayContainer;

class ExcludedFilesCollection extends ArrayContainer
{
    public function excludeFromFinder(Finder $finder)
    {
        $finder->exclude(array_values($this->toArray()));
    }
}
