<?php

namespace Tapestry\Modules\Collectors\Mutators;

use Tapestry\Modules\Source\SourceInterface;

/**
 * Class IsIgnoredMutator
 *
 * Any files not handled by renders are "ignored"; these are files that should be included in the collected
 * list of files due to them maybe being a dependency of other files (e.g templates, partials, etc) but that
 * should be ignored at compile time given they are a resource and not a source.
 *
 * Ignored files are different to Excluded files, with the later actually being excluded from the collected
 * list of files.
 *
 * Any path containing an underscore (_) is ignored by default unless its found within the $exclusions array.
 *
 * @package Tapestry\Modules\Collectors\Mutators
 */
final class IsIgnoredMutator implements MutatorInterface
{
    /**
     * @var array
     */
    private $ignorePaths;

    /**
     * @var array
     */
    private $exclusions;

    /**
     * IsIgnoredMutator constructor.
     *
     * Exclusions can be the source paths of Content Types, e.g _blog which would otherwise have it's content set to
     * be ignored, when it should actually be parsed.
     *
     * @param array $ignorePaths
     * @param array $exclusions
     */
    public function __construct(array $ignorePaths = [], array $exclusions = [])
    {
        $this->ignorePaths = $ignorePaths;
        $this->exclusions = $exclusions;
    }

    /**
     * @param SourceInterface $source
     */
    public function mutate(SourceInterface &$source)
    {
        $relativePath = $source->getRelativePath();

        foreach ($this->exclusions as $exclusion) {
            if (str_contains($relativePath, $exclusion)) {
                $source->setIgnored(false);
                return;
            }
        }

        foreach ($this->ignorePaths as $ignoredPath) {
            if (str_contains($relativePath, $ignoredPath)) {
                $source->setIgnored();
                return;
            }
        }

        // Paths containing underscores are ignored by default.
        foreach (explode('/', str_replace('\\', '/', $relativePath)) as $pathItem) {
            if (substr($pathItem, 0, 1) === '_') {
                $source->setIgnored();
                return;
            }
        }

        $source->setIgnored(false);
    }
}