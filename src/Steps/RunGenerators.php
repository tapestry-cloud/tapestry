<?php

namespace Tapestry\Steps;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Tapestry\Modules\Collectors\CollectorCollection;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Modules\ContentTypes\ContentTypeCollection;

class RunGenerators implements Step
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
        //
        // Replace the ParseContentTypes Step with this
        // That Step basically loops over all project files and injects ContentType data for files
        // that have a use.
        //
        // Replace the FileGenerator class with a MemorySource or create a new extension of AbstractSource
        // called GeneratedSource.
        //
        // @todo this.
        //

        return false;
    }
}
