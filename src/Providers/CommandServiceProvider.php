<?php namespace Tapestry\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Tapestry\Console\Application;
use Tapestry\Console\Commands\BuildCommand;
use Tapestry\Console\Commands\InitCommand;
use Tapestry\Console\Commands\SelfUpdateCommand;
use Tapestry\Tapestry;

class CommandServiceProvider extends AbstractServiceProvider
{

    /**
     * @var array
     */
    protected $provides = [
        Application::class,
        InitCommand::class
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add(InitCommand::class)
            ->withArguments([
                \Symfony\Component\Filesystem\Filesystem::class,
                \Symfony\Component\Finder\Finder::class
            ]);

        $this->getContainer()->add(BuildCommand::class)
            ->withArguments([
                Tapestry::class,
                $this->getContainer()->get('Compile.Steps')
            ]);

        $this->getContainer()->add(SelfUpdateCommand::class)
            ->withArguments([
                \Symfony\Component\Filesystem\Filesystem::class,
                \Symfony\Component\Finder\Finder::class,
            ]);

        $this->getContainer()->add(Application::class)
            ->withArguments([
                Tapestry::class,
                [
                    $this->getContainer()->get(InitCommand::class),
                    $this->getContainer()->get(BuildCommand::class),
                    $this->getContainer()->get(SelfUpdateCommand::class)
                ]
            ]);
    }
}