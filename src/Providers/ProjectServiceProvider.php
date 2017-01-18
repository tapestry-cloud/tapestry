<?php

namespace Tapestry\Providers;

use Tapestry\Entities\Project;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ProjectServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Project::class,
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
        $container->share(Project::class, function() use ($container) {
            $project = new Project($container->get('currentWorkingDirectory'), $container->get('environment'));
            return $project;
        });

    }
}
