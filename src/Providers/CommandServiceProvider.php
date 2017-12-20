<?php

namespace Tapestry\Providers;

use Tapestry\Tapestry;
use Tapestry\Console\Application;
use Tapestry\Console\Commands\InitCommand;
use Tapestry\Console\Commands\BuildCommand;
use Tapestry\Console\Commands\SelfUpdateCommand;
use League\Container\ServiceProvider\AbstractServiceProvider;

class CommandServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Application::class,
        InitCommand::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function register()
    {
        $container = $this->getContainer();
        $commands = [];

        $container->add(InitCommand::class)
            ->withArguments([
                \Symfony\Component\Filesystem\Filesystem::class,
                \Symfony\Component\Finder\Finder::class,
            ]);

        array_push($commands, $container->get(InitCommand::class));

        $this->getContainer()->add(BuildCommand::class)
            ->withArguments([
                Tapestry::class,
                $this->getContainer()->get('Compile.Steps'),
            ]);

        array_push($commands, $container->get(BuildCommand::class));

        if (strlen(\Phar::running() > 0)) {
            $container->add(SelfUpdateCommand::class)
                ->withArguments([
                    \Symfony\Component\Filesystem\Filesystem::class,
                    \Symfony\Component\Finder\Finder::class,
                ]);

            array_push($commands, $container->get(SelfUpdateCommand::class));
        }

        $container->share(Application::class)
            ->withArguments([
                Tapestry::class,
                $commands,
            ]);
    }
}
