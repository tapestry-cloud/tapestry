<?php

namespace Tapestry\Steps;

use Tapestry\Modules\Source\AbstractSource;
use Tapestry\Step;
use Tapestry\Entities\Project;
use Tapestry\Modules\ContentTypes\ContentType;
use Symfony\Component\Console\Output\OutputInterface;

class ParseContentTypes implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Exception
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        // @todo Replace the FileGenerator class with a MemorySource or create a new extension of AbstractSource called GeneratedSource.
        //
        // Add use references to the AST, if a file uses files from a content type then the
        // AST need's updating to reflect that so that if the content-type or any of the files
        // that are included within the use change the file itself will get re-compiled too.
        // @todo this needs to add to the AST (see above paragraph)
        //
        // Foreach file that is found to have a use statement it needs the data injecting.
        // However if it has a list of generators they need to be run in the order that they
        // are listed.
        //
        // For example a file could have the following two generators:
        // - TaxonomyArchiveGenerator
        // - PaginationGenerator
        //
        // The TaxonomyArchiveGenerator would create a new GeneratedSource record for each
        // taxonomy listed e.g hello.phtml, world.phtml.
        //
        // Next the PaginationGenerator would execute and split replace each GeneratedSource
        // with one or more GeneratedSource based upon how many pages worth of content was
        // injected.
        //
        // All of this requires that each page with a use array has those related items injected.
        // In version 1 of Tapestry this was achieved by passing in an array of files from the
        // ContentType they originate from. It may be a good idea to instead pass in a container
        // class like a SourceCollection and a TaxonomyCollection.

        /**
         * @var AbstractSource $source
         */
        foreach ($project->allSources()->where(function($item){return $item instanceof AbstractSource;}) as $source) {
            /** @var string[] $uses */
            if (! $uses = $source->getData('use')) {
                continue;
            }

            foreach ($uses as $use) {
                if (strpos($use, '_') !== false) {
                    // This is a request for a taxonomy from a content type e.g `blog_categories`
                    $useParts = explode('_', $use);
                    $useContentType = array_shift($useParts);
                    $useTaxonomy = implode('_', $useParts);

                    /** @var ContentType $contentType */
                    if (! $contentType = $project['content_types.'.$useContentType]) {
                        continue;
                    }

                    $source->setData($use.'_items', $contentType->getTaxonomy($useTaxonomy)->getFileList());

                    // If the file doesn't have a generator set then we need to define one
                    if (! $source->hasData('generator')) {
                        // do we _need_ to add a generator here?
                        $source->setData('generator', ['TaxonomyIndexGenerator']);
                    }
                    unset($useParts, $useContentType, $useTaxonomy);
                } else {
                    // This is a request for the items in a content type e.g `blog`
                    /** @var ContentType $contentType */
                    if (! $contentType = $project['content_types.'.$use]) {
                        continue;
                    }

                    $source->setData($use.'_items', $contentType->getSourceList());
                }
            }

            unset($file, $uses, $use, $contentType);
        }

        /** @var ContentType $contentType */
        foreach ($project['content_types']->all() as $contentType) {
            $contentType->mutateProjectSources($project);
        }

        return true;
    }
}
