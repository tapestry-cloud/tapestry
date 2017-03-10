<?php

namespace Tapestry\Console;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Class Input
 *
 * The purpose of this extension to ArgvInput exists to pass Argv input to Tapestry on construction. Because Tapestry
 * requires knowledge of certain command line properties as defined in DefaultInputDefinition but isn't aware until
 * Application invocation of command specific input definitions the provided ArgvInput and ArrayInput classes could not
 * be used because they end up throwing errors when a command specific argument or option is included.
 *
 * This allows Tapestry to see --env, --site-dir and --dist-dir options which it needs before configuration and/or site
 * Kernel can be loaded; this was further compounded due to the site Kernel being able to add its own commands to the
 * Application.
 *
 * BuildCommand and any subsequent command should upon firing pass along its Input to Tapestry via the setInput method.
 *
 * @package Tapestry\Console
 */
class Input extends ArgvInput
{
    private $tokens;

    /**
     * Input constructor.
     * @param array $input
     * @param InputDefinition|null $definition
     */
    public function __construct(array $input = [], InputDefinition $definition = null)
    {
        parent::__construct($input);

        if (strpos($input[0], '.php')) {
            array_shift($input);
        }

        $this->tokens = $input;

        if (! is_null($definition)) {
            $this->bind($definition);
        }

        // foreach ($input as $key => $value) {
        //     if ($key === 'command') {
        //         continue; // we dont care about the command
        //     } else if (is_numeric($key)) {
        //         if (substr($value, 0, 2) === '--') {
        //             $value = substr($value, 2);
        //         }
        //         $this->options[$value] = true;
        //     } else {
        //         if (substr($key, 0, 2) === '--') {
        //             $key = substr($key, 2);
        //         }
        //         $this->options[$key] = $value;
        //     }
        // } unset($key, $value);
    }

    protected function parse()
    {
        foreach ($this->tokens as $key => $value) {
            if ($key === 'command') {
                continue; // we dont care about the command
            }

            // Is $this->input in the form expected from ArgvInput?
            if (is_integer($key) && strpos($value, '=') !== false) {
                $value = explode('=', $value);
                $key = $value[0];
                $value = isset($value[1]) ? $value[1] : '';
            }

            if (is_numeric($key)) {
                if (substr($value, 0, 2) === '--') {
                    $value = substr($value, 2);
                    $this->options[$value] = true;
                } else if (substr($value, 0, 2) === '--') {
            }

        }
        //parent::parse();
    }
}