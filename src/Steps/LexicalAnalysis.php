<?php

namespace Tapestry\Steps;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class LexicalAnalysis implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        //
        // Evaluate the symbol table and build the dependency graph
        //
        // e.g. Kernel -> index.phtml -> BlogPosts (paginated 3 per page) -> post-1.md
        //                                                                -> post-2.md
        //                                                                -> post-3.md
        //
        return true;
    }
}
