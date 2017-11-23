<?php

namespace Tapestry\Console\Commands;

use Tapestry\Tapestry;
use Tapestry\Generator;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Input\InputOption;
use Tapestry\Exceptions\InvalidConsoleInputException;

class BuildCommand extends Command
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * @var array
     */
    private $steps;

    /**
     * InitCommand constructor.
     *
     * @param Tapestry $tapestry
     * @param array    $steps
     */
    public function __construct(Tapestry $tapestry, array $steps)
    {
        parent::__construct();
        $this->tapestry = $tapestry;
        $this->steps = $steps;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Build Project.');

        $this->getDefinition()->addOptions(
            [
                new InputOption('--clear', null, InputOption::VALUE_NONE, 'Clear the destination path and disable caching.'),
                new InputOption('--json', false, InputOption::VALUE_NONE, 'Output a json file containing the current build state.'),
                new InputOption('--no-write', false, InputOption::VALUE_NONE, 'When set Tapestry will build the state but not commit to the file system.'),
                new InputOption('--auto-publish', false, InputOption::VALUE_NONE, 'Set Tapestry to consider draft posts with a date <= now as scheduled.'),
            ]
        );
    }

    protected function fire()
    {
        try {
            $this->tapestry->setInput($this->input);
            $this->tapestry->setOutput($this->output);
            $this->tapestry->validateInput();
        } catch (InvalidConsoleInputException $e) {
            $this->output->writeln('<error>[!]</error> '.$e->getMessage().' Doing nothing and exiting.');

            return 1;
        }

        $generator = new Generator($this->steps, $this->tapestry);

        /** @var Project $project */
        $project = $this->tapestry->getContainer()->get(Project::class);

        return $generator->generate($project, $this->output);
    }
}
