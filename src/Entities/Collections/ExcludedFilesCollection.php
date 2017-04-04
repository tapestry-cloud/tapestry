<?php

namespace Tapestry\Entities\Collections;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\ArrayContainer;
use Symfony\Component\Finder\Finder;

class ExcludedFilesCollection extends ArrayContainer
{
    /**
     * By default Tapestry will ignore any path with an underscore, unless it's added to this exceptions list.
     * This allows you to have content types that load from an underscore path.
     *
     * @var array
     */
    private $underscoreExceptions = [];

    /**
     * @param string $exception
     */
    public function addUnderscoreException($exception)
    {
        array_push($this->underscoreExceptions, $exception);
    }

    /**
     * @param Finder $finder
     */
    public function excludeFromFinder(Finder $finder)
    {
        $hasUnderscoreExceptions = (count($this->underscoreExceptions) > 0);
        $finder->exclude(array_values($this->toArray()));
        $finder->filter(function ($item) use ($hasUnderscoreExceptions) {
            /** @var SplFileInfo $item */
            $relativePath = $item->getRelativePath();
            if ($hasUnderscoreExceptions === true) {
                foreach ($this->underscoreExceptions as $exception) {
                    if (str_contains($relativePath, $exception)) {
                        return true;
                    }
                }
            }

            foreach (explode('/', str_replace('\\', '/', $relativePath)) as $pathItem) {
                if (substr($pathItem, 0, 1) === '_') {
                    return false;
                }
            }
            return true;
        });
    }
}
