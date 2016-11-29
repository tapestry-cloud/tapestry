<?php

namespace Tapestry\Modules\ContentTypes;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Entities\Generators\FileGenerator;
use Tapestry\Entities\Project;
use Tapestry\Step;

class ParseContentTypes implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        //
        // Loop over all project files, those that have a data source via the `use` method should have the relevant
        // content type data source passed to them. Those that have generators associated with them (such as those using
        // a content types taxonomy) should be passed through a generator and the original File removed from the Project
        // file list, having been replaced by a FileGenerator.
        //
        // When Writing, if you find a FileGenerator simply execute its generate() method and it should do the rest. This
        // is to be used for pagination and taxonomy output where pages are generated that do not exist in the source path.
        //

        /** @var File $file */
        foreach ($project['files']->all() as $file) {
            if (!$uses = $file->getData('use')) {
                continue;
            }

            foreach ($uses as $use) {
                // Is this file using the content type items, or its taxonomy?
                if (strpos($use, '_') !== false) {
                    $useParts = explode('_', $use);
                    $useContentType = array_shift($useParts);
                    $useTaxonomy = implode('_', $useParts);

                    /** @var ContentType $contentType */
                    if (!$contentType = $project['content_types.'.$useContentType]) {
                        continue;
                    }
                    $file->setData([$use.'_items' => $contentType->getTaxonomy($useTaxonomy)->getFileList()]);

                    // If the file doesn't have a generator set then we need to define one
                    if (!$fileGenerator = $file->getData('generator')) {
                        // do we _need_ to add a generator here?
                        $file->setData(['generator' => ['TaxonomyIndexGenerator']]);
                    }
                } else {
                    /** @var ContentType $contentType */
                    if (!$contentType = $project['content_types.'.$use]) {
                        continue;
                    }
                    $file->setData([$use.'_items' => $contentType->getFileList()]);
                }
            }

            $project->replaceFile($file, new FileGenerator($file));
        }
        unset($file, $uses, $use, $contentType);

        /** @var ContentType $contentType */
        foreach ($project['content_types']->all() as $contentType) {
            $contentType->mutateProjectFiles($project);
        }

        return true;
    }
}
