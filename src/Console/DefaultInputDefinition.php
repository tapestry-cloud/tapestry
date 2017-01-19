<?php

namespace Tapestry\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class DefaultInputDefinition extends InputDefinition {
    public function __construct(array $definition = [])
    {
        $definition = array_merge($definition, [
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message'),
            new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Do not output any message'),
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version'),
            new InputOption('--ansi', '', InputOption::VALUE_NONE, 'Force ANSI output'),
            new InputOption('--no-ansi', '', InputOption::VALUE_NONE, 'Disable ANSI output'),
            new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Do not ask any interactive question'),

            new InputOption('--site-dir', null, InputOption::VALUE_REQUIRED, 'The site directory', getcwd()),
            new InputOption('--dist-dir', null, InputOption::VALUE_REQUIRED, 'The destination directory', getcwd() . DIRECTORY_SEPARATOR . 'local_build'),
            new InputOption('--env', 'e', InputOption::VALUE_REQUIRED, 'Site environment', 'local'),
            new InputOption('--stopwatch', 's', InputOption::VALUE_NONE, 'Time how long the build took'),
        ]);
        parent::__construct($definition);
    }
}