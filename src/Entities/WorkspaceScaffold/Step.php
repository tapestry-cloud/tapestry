<?php

namespace Tapestry\Entities\WorkspaceScaffold;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\WorkspaceScaffold;

abstract class Step
{
    abstract public function __invoke(OutputInterface $output, WorkspaceScaffold $scaffold);
}