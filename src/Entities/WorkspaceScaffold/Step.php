<?php

namespace Tapestry\Entities\WorkspaceScaffold;

use Symfony\Component\Console\Input\InputInterface;
use Tapestry\Entities\WorkspaceScaffold;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Step
{
    abstract public function __invoke(InputInterface $input, OutputInterface $output, WorkspaceScaffold $scaffold);
}
