<?php

namespace Tapestry\Steps;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Tapestry\Modules\Collectors\CollectorCollection;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Modules\ContentTypes\ContentTypeCollection;

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

        foreach ($collection->collect() as $source) {
            $contentType = $contentTypes->bucketSource($source);
            $output->writeln('[+] File ['.$source->getRelativePathname().'] bucketed into content type ['.$contentType->getName().']');
        }

        return true;
    }
}
