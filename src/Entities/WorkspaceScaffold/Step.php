<?php

namespace Tapestry\Entities\WorkspaceScaffold;

use Tapestry\Entities\WorkspaceScaffold;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Step
{
    abstract public function __invoke(OutputInterface $output, WorkspaceScaffold $scaffold);
}
