<?php namespace Tapestry\Console;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputOption;
use Tapestry\Tapestry;

class Application extends ConsoleApplication
{

    /**
     * @var Tapestry
     */
    protected $tapestry;

    /**
     * Application constructor.
     * @param Tapestry $tapestry
     * @param array $commands
     */
    public function __construct(Tapestry $tapestry, array $commands = array())
    {
        parent::__construct('Tapestry', $tapestry::VERSION);
        $this->getDefinition()->addOptions(
            [
                new InputOption('--site-dir', null, InputOption::VALUE_REQUIRED, 'The site directory', getcwd()),
                new InputOption('--env', 'e', InputOption::VALUE_REQUIRED, 'Site environment', 'local'),
                new InputOption('--stopwatch', 's', InputOption::VALUE_NONE, 'Time how long the build took')
            ]
        );
        $this->tapestry = $tapestry;
        $this->addCommands($commands);
    }

    /**
     * Returns the long version of the application.
     *
     * @return string The long application version
     */
    public function getLongVersion()
    {
        if ('UNKNOWN' !== $this->getName() && 'UNKNOWN' !== $this->getVersion()) {
            return sprintf('<info>%s</info> version <comment>%s</comment>, environment <comment>%s</comment>',
                $this->getName(), $this->getVersion(), $this->tapestry['environment']);
        }
        return '<info>Console Tool</info>';
    }
}