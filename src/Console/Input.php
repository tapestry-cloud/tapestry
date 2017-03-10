<?php

namespace Tapestry\Console;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\StringInput;

class Input extends StringInput
{
    /**
     * Input constructor.
     * @param array $input
     * @param InputDefinition|null $definition
     */
    public function __construct(array $input = [], InputDefinition $definition = null)
    {
        if (strpos($input[0], '.php')) {
            array_shift($input);
        }

        $commandLineInput = '';
        foreach ($input as $key => $value) {
            if ($key === 'command') {
                $commandLineInput = $value;
            } else if (is_numeric($key)) {
                $commandLineInput .= $value;
            } else {
                $commandLineInput .= $key . '=' . $value;
            }
            $commandLineInput .= ' ';
        } unset($key, $value);

        $commandLineInput = substr($commandLineInput, 0, -1);
        parent::__construct($commandLineInput);

        if (! is_null($definition)) {
            $this->bind($definition);
        }
    }
}