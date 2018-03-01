<?php

namespace Tapestry\Steps;

use Tapestry\Modules\Collectors\CollectorCollection;
use Tapestry\Modules\ContentTypes\ContentTypeCollection;
use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class RunContentCollectors implements Step
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
        /** @var CollectorCollection $collection */
        $collection = $project->get('content_collectors');

        /** @var ContentTypeCollection $contentTypes */
        $contentTypes = $project->get('content_types');

        $files = $collection->collect();

        foreach ($files as $file)
        {
            if (! $contentType = $contentTypes->find($file->getRelativePath())) {
                $contentType = $contentTypes->get('*');
            } else {
                $contentType = $contentTypes->get($contentType);
            }

            $contentType->addFile($file);
            $project->addFile($file);

            $output->writeln('[+] File ['.$file->getRelativePathname().'] bucketed into content type ['.$contentType->getName().']');
        }

        return true;
    }
}
