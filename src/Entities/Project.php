<?php namespace Tapestry\Entities;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\ArrayContainer;

class Project extends ArrayContainer
{
    public function __construct(array $steps, $currentWorkingDirectory, $environment)
    {
        parent::__construct([
            'steps' => $steps,
            'cwd' => $currentWorkingDirectory,
            'env' => $environment
        ]);
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->set('output', $output);
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->get('output');
    }

    public function compile()
    {
        foreach($this['steps'] as $step) {
            $this->getOutput()->writeln('Executing step ['. class_basename($step) .']');
            if (! $step($this)){
                exit(1);
            }
        }
    }
}