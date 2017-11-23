<?php

namespace Tapestry\Console;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Class Input.
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
 */
class Input extends ArgvInput
{
    /**
     * Filtered input tokens: this array will only contain input that matches the InputDefinition.
     * @var array
     */
    private $filtered = [];

    /**
     * Input constructor.
     * @param array $argv
     * @param InputDefinition $definition
     */
    public function __construct(array $argv, InputDefinition $definition)
    {
        array_shift($argv);

        $this->definition = $definition;
        $this->filter($argv);
        $this->setTokens($this->filtered);
        $this->bind($definition);
        $this->validate();
    }

    private function filter(array $input)
    {
        $parseOptions = true;
        while (null !== $token = array_shift($input)) {
            if ($parseOptions && '' == $token) {
                $this->checkArgument($token);
            } elseif ($parseOptions && '--' == $token) {
                $parseOptions = false;
            } elseif ($parseOptions && 0 === strpos($token, '--')) {
                $this->checkLongOption($token);
            } elseif ($parseOptions && '-' === $token[0] && '-' !== $token) {
                $this->checkShortOption($token);
            } else {
                $this->checkArgument($token);
            }
        }
    }

    private function checkArgument($token)
    {
        if ($this->definition->hasArgument($token)) {
            array_push($this->filtered, $token);
        }
    }

    private function checkLongOption($token)
    {
        $name = substr($token, 2);
        if (false !== $pos = strpos($name, '=')) {
            if (0 === strlen($value = substr($name, $pos + 1))) {
                array_unshift($this->filtered, null);
            }
            $this->checkHasOption(substr($name, 0, $pos), $token);
        } else {
            $this->checkHasOption($name, $token);
        }
    }

    private function checkHasOption($name, $token)
    {
        if ($this->definition->hasOption($name)) {
            array_push($this->filtered, $token);
        }
    }

    private function checkShortOption($token)
    {
        $name = substr($token, 1);

        if (strlen($name) > 1) {
            if ($this->definition->hasShortcut($name[0]) && $this->definition->getOptionForShortcut($name[0])->acceptValue()) {
                // an option with a value (with no space)
                $this->checkHasShortcut($name[0], $token);
            } else {
                $len = strlen($name);
                for ($i = 0; $i < $len; $i++) {
                    $this->checkHasShortcut($name[$i], $token);
                }
            }
        } else {
            $this->checkHasShortcut($name, $token);
        }
    }

    private function checkHasShortcut($name, $token)
    {
        if ($this->definition->hasShortcut($name)) {
            array_push($this->filtered, $token);
        }
    }
}
