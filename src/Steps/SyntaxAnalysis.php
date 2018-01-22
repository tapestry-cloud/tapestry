<?php

namespace Tapestry\Steps;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Project;
use Tapestry\Step;

class SyntaxAnalysis implements Step
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
        // Identify all source files and build the initial symbol table
        //
        // This step is essentially a refactoring of LoadSourceFiles
        //

        //
        // For this to be written the File object will need to be refactored
        //

        return true;
    }
}