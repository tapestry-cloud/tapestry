<?php

namespace Tapestry\Tests\Mocks;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\WorkspaceScaffold;
use Tapestry\Entities\WorkspaceScaffold\Step;

class TestWorkspaceScaffoldStep extends Step
{
    public function __invoke(InputInterface $input, OutputInterface $output, WorkspaceScaffold $scaffold)
    {
        $model = $scaffold->getModel();
        $model['hello'] = 'world';
        $scaffold->setModel($model);
        return true;
    }
}