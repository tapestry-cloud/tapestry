<?php namespace Tapestry\Modules\ContentTypes;

use Symfony\Component\Console\Output\OutputInterface;
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



        $n =1;
        // ...
    }
}
