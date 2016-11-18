<?php namespace Tapestry\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Tapestry\Entities\Project;
use Tapestry\Plates\Engine;
use Tapestry\Plates\Extensions\Site;
use Tapestry\Plates\Extensions\Url;

class PlatesServiceProvider extends AbstractServiceProvider
{

    /**
     * @var array
     */
    protected $provides = [
        Engine::class
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

        /** @var Project $project */
        $project = $container->get(Project::class);

        $this->getContainer()->share(Engine::class, function () use ($project, $container) {
            $engine = new Engine($project->sourceDirectory, 'phtml');
            $engine->loadExtension($container->get(Site::class));
            $engine->loadExtension($container->get(Url::class));
            return $engine;
        });
    }
}