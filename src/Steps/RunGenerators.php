<?php

namespace Tapestry\Steps;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

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
        // @todo Foreach File run Generators found until all Generators have been ran.
        //
        // Note: I don't think the AST need be
        //
        // 1 Create a new GeneratedSource from the AbstractSource and set the original Source's
        //   ignore flag to true so it doesn't get compiled itself.
        // 2 Run each generator until they have all been ran.

        return false;
    }
}
