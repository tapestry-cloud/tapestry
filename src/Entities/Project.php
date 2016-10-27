<?php namespace Tapestry\Entities;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\ArrayContainer;
use Tapestry\Tapestry;

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
     * @return Tapestry
     */
    public function getTapestry()
    {
        return $this->get('tapestry');
    }

    /**
     * @param Tapestry $tapestry
     */
    public function setTapestry(Tapestry $tapestry)
    {
        $this->set('tapestry', $tapestry);
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

            $step = $this->getTapestry()->getContainer()->get($step);

            $this->getOutput()->writeln('Executing step ['. class_basename($step) .']');
            if (! $step($this)){
                exit(1);
            }
        }
    }
}