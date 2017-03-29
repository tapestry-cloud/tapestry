<?php

namespace SiteTen;

use Tapestry\Console\Commands\Command;

class TestKernelCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('hello')
            ->setDescription('Hello World From A Kernel Loaded Command');
    }

    /**
     * @return int
     */
    protected function fire()
    {
        $this->info('Hello world! This command was loaded via a site Kernel.');

        return 0;
    }
}
