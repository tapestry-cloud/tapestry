<?php namespace Tapestry\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Tapestry\Console\Application;
use Tapestry\Console\Commands\BuildCommand;
use Tapestry\Console\Commands\InitCommand;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Plates\Engine;
use Tapestry\Plates\Extensions\Site;
use Tapestry\Tapestry;

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

        /** @var Project $project */
        $project = $this->getContainer()->get(Project::class);

        $this->getContainer()->share(Engine::class, function () use ($project) {
            $engine = new Engine($project->sourceDirectory, 'phtml');
            $engine->loadExtension($this->getContainer()->get(Site::class));
            return $engine;
        });
    }
}