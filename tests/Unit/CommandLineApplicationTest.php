<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Console\Output\Output;
use Tapestry\Tapestry;
use Tapestry\Tests\TestCase;

class CommandLineApplicationTest extends TestCase
{
    public function testApplicationVersion()
    {
        $output = $this->runCommand('', '--version', ['verbosity' => Output::VERBOSITY_NORMAL]);
        $this->assertEquals('Tapestry version '.Tapestry::VERSION.', environment local', trim($output->getDisplay()));
    }
}
