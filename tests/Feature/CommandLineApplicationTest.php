<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;
use Tapestry\Tapestry;

class CommandLineApplicationTest extends CommandTestBase
{
    public function testApplicationVersion()
    {
        $output = $this->runCommand('', '--version');
        $this->assertEquals('Tapestry version '.Tapestry::VERSION.', environment local', trim($output->getDisplay()));
    }
}
