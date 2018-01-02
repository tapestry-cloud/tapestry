<?php

namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;
use Tapestry\Entities\Project;
use Tapestry\Exceptions\InvalidConsoleInputException;
use Tapestry\Tapestry;

class ServeCommand extends Command
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * ServeCommand constructor.
     *
     * @param Tapestry $tapestry
     */
    public function __construct(Tapestry $tapestry)
    {
        parent::__construct();
        $this->tapestry = $tapestry;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('serve')
            ->setDescription('Serve a project using the PHP development server.')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve on.', '127.0.0.1')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'The network port to serve on.', 3000);
    }

    /**
     * @return int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
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

        /** @var Project $project */
        $project = $this->tapestry->getContainer()->get(Project::class);

        if (!file_exists($project->destinationDirectory)) {
            if ($this->output->isDebug()){
                $this->error("The path [{$project->destinationDirectory}] does not exist.");
            }
            $this->error("The project hasn't been built yet, please run the build command first.");
            return 1;
        }

        $command = sprintf('%s -S %s:%s -t %s',
            trim((new PhpExecutableFinder())->find(false)),
            $this->input->getOption('host'),
            $this->input->getOption('port'),
            $project->destinationDirectory
        );

        if ($this->output->isDebug()) {
            $this->info('Executing ['. $command .']');
        } else {
            $this->output->writeln('Starting development server on: <info>'.$this->input->getOption('host').':'.$this->input->getOption('port').'</info>.');
        }

        passthru($command);
        return 0;
    }
}