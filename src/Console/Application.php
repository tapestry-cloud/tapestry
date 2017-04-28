<?php

namespace Tapestry\Console;

use Tapestry\Version;
use Tapestry\Tapestry;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * @var Tapestry
     */
    protected $tapestry;

    /**
     * Application constructor.
     *
     * @param Tapestry $tapestry
     * @param array    $commands
     */
    public function __construct(Tapestry $tapestry, array $commands = [])
    {
        parent::__construct('Tapestry', Version::build());
        $this->tapestry = $tapestry;
        $this->addCommands($commands);
    }

    protected function getDefaultInputDefinition()
    {
        return new DefaultInputDefinition();
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
