<?php namespace Tapestry\Console\Commands;

use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Tapestry;

class BuildCommand extends Command
{
    /**
     * Current Working Directory as set by user input --site-dir or getcwd() by default.
     * @var string
     */
    private $currentWorkingDirectory;
    /**
     * @var array
     */
    private $steps;

    /**
     * @var string
     */
    private $environment;
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * InitCommand constructor.
     * @param Tapestry $tapestry
     * @param array $steps
     * @param string $currentWorkingDirectory
     * @param $environment
     */
    public function __construct(Tapestry $tapestry, array $steps, $currentWorkingDirectory, $environment)
    {
        parent::__construct();
        $this->currentWorkingDirectory = $currentWorkingDirectory;
        $this->steps = $steps;
        $this->environment = $environment;
        $this->tapestry = $tapestry;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Build Project.');
    }

    protected function fire()
    {
        if (!file_exists($this->currentWorkingDirectory)) {
            $this->output->writeln('<error>[!]</error> The site directory ['. $this->currentWorkingDirectory .'] does not exist. Doing nothing and exiting.');
            exit(1);
        }

        // Lets use full paths.
        if (! $this->currentWorkingDirectory = realpath($this->currentWorkingDirectory)) {
            $this->output->writeln('<error>[!]</error> Sorry there has been an error identifying the site directory. Doing nothing and exiting.');
            exit(1);
        }

        $generator = new Generator($this->steps, $this->tapestry);
        $project = new Project($this->currentWorkingDirectory, $this->environment);
        $this->tapestry->getContainer()->add(Project::class, $project);
        $generator->generate($project, $this->output);
        return 0;
    }
}