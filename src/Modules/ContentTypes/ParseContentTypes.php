<?php namespace Tapestry\Modules\ContentTypes;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Step;

class ParseContentTypes implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        // Iterate over each content type and process each file within the project file list. This means we only need to
        // mutate the File object within the $project['files'] container.

        /** @var ContentType $contentType */
        //foreach ($project['content_types'] as $contentType) {
        //
        //}

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
        foreach ($project['files'] as $file) {
            if (!$uses = $file->getData('use')) {
                continue;
            }

            foreach ($uses as $use) {
                /** @var ContentType $contentType */
                if (! $contentType = $project['content_types.' . $use]) { continue; }
                $file->setData([$use . '_items' => $contentType->getFileList()]);
            }
        } unset($file);

        $n =1;
        // ...
    }
}
