<?php

namespace Tapestry\Tests\Mocks;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\WorkspaceScaffold;
use Tapestry\Entities\WorkspaceScaffold\Step;

class TestInvalidWorkspaceScaffoldStep extends Step
{
    public function __invoke(OutputInterface $output, WorkspaceScaffold $scaffold)
    {
        return 'hello world';
    }
}