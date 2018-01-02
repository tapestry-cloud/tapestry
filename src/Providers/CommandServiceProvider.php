<?php

namespace Tapestry\Providers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tapestry\Console\Commands\ServeCommand;
use Tapestry\Entities\Project;
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
    ];

    private $commands = [
        InitCommand::class,
        ServeCommand::class,
        BuildCommand::class,
        SelfUpdateCommand::class,
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
        foreach ($this->commands as &$command){
            $cmd = explode('\\', $command);
            call_user_func_array([$this, "register". end($cmd)], []);
            $command = $this->getContainer()->get($command);
        } unset($command);

        $this->getContainer()->share(Application::class)
            ->withArguments([
                Tapestry::class,
                $this->commands,
            ]);
    }

    /**
     * Register init command.
     *
     * @return void
     */
    protected function registerInitCommand()
    {
        $this->getContainer()->add(InitCommand::class)
            ->withArguments([
                \Symfony\Component\Filesystem\Filesystem::class,
                \Symfony\Component\Finder\Finder::class,
            ]);
    }

    /**
     * Register serve command.
     *
     * @return void
     */
    protected function registerServeCommand()
    {
        $this->getContainer()->add(ServeCommand::class)
            ->withArgument(Tapestry::class);
    }

    /**
     * Register build command.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return void
     */
    protected function registerBuildCommand()
    {
        $steps = $this->getContainer()->get('Compile.Steps');
        $this->getContainer()->add(BuildCommand::class)
            ->withArguments([
                Tapestry::class,
                $steps,
            ]);
    }

    /**
     * Register self update command if executed within a Phar.
     *
     * @return void
     */
    protected function registerSelfUpdateCommand()
    {
        $this->getContainer()->add(SelfUpdateCommand::class)
            ->withArguments([
                \Symfony\Component\Filesystem\Filesystem::class,
                \Symfony\Component\Finder\Finder::class,
            ]);
    }
}
