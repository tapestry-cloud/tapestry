<?php

namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputOption;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Tapestry;

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
            ]
        );
    }

    protected function fire()
    {
        $currentWorkingDirectory = $this->input->getOption('site-dir');
        $environment = $this->input->getOption('env');

        if (!file_exists($currentWorkingDirectory)) {
            $this->output->writeln('<error>[!]</error> The site directory ['.$currentWorkingDirectory.'] does not exist. Doing nothing and exiting.');
            exit(1);
        }

        // Lets use full paths.
        if (!$currentWorkingDirectory = realpath($currentWorkingDirectory)) {
            $this->output->writeln('<error>[!]</error> Sorry there has been an error identifying the site directory. Doing nothing and exiting.');
            exit(1);
        }

        $generator = new Generator($this->steps, $this->tapestry);
        $project = new Project($currentWorkingDirectory, $environment);

        $project->set('cmd_options', $this->input->getOptions());

        $this->tapestry->getContainer()->add(Project::class, $project);
        $generator->generate($project, $this->output);

        return 0;
    }
}
