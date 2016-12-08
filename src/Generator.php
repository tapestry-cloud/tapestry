<?php

namespace Tapestry;

use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class Generator
{
    /**
     * @var array
     */
    private $steps;
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * Generator constructor.
     *
     * @param array    $steps
     * @param Tapestry $tapestry
     */
    public function __construct(array $steps, Tapestry $tapestry)
    {
        $this->steps = $steps;
        $this->tapestry = $tapestry;
    }

    public function generate(Project $project, OutputInterface $output)
    {
        $output->writeln('Generating site from <comment>'.$project->sourceDirectory.'</comment> to <comment>'.$project->destinationDirectory.'</comment>');

        foreach ($this->steps as $step) {
            /** @var Step $step */
            $step = $this->tapestry->getContainer()->get($step);
            $output->writeln('Executing step ['.class_basename($step).']');
            if (! $step->__invoke($project, $output)) {
                exit(1);
            }
        }
    }
}
