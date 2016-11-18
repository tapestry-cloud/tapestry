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
        $container = $this->getContainer();

        $container->add(InitCommand::class)
            ->withArguments([
                \Symfony\Component\Filesystem\Filesystem::class,
                \Symfony\Component\Finder\Finder::class,
                $container->get('currentWorkingDirectory')
            ]);

        $container->add(BuildCommand::class)
            ->withArguments([
                Tapestry::class,
                $container->get('Compile.Steps'),
                $container->get('currentWorkingDirectory'),
                $container->get('environment')
            ]);

        $container->add(SelfUpdateCommand::class)
            ->withArguments([
                \Symfony\Component\Filesystem\Filesystem::class,
                \Symfony\Component\Finder\Finder::class,
            ]);

        $container->add(Application::class)
            ->withArguments([
                Tapestry::class,
                [
                    $container->get(InitCommand::class),
                    $container->get(BuildCommand::class),
                    $container->get(SelfUpdateCommand::class)
                ]
            ]);
    }
}