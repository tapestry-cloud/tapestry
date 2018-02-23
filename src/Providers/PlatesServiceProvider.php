<?php

namespace Tapestry\Providers;

use League\Plates\Engine;
use Tapestry\Entities\Project;
use Tapestry\Modules\Plates\Extensions\Url;
use Tapestry\Modules\Plates\Extensions\Site;
use Tapestry\Modules\Plates\Extensions\Environment;
use Tapestry\Modules\Plates\Extensions\RenderProjectFile;
use League\Container\ServiceProvider\AbstractServiceProvider;

class PlatesServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Engine::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    public function register()
    {
        $container = $this->getContainer();

        /** @var Project $project */
        $project = $container->get(Project::class);

        $intermediatePath = $project->currentWorkingDirectory.DIRECTORY_SEPARATOR.'.compileTmp';

        if (! file_exists($intermediatePath)) {
            if (! mkdir($intermediatePath)) {
                throw new \Exception('Could not create folder at ['.$intermediatePath.']');
            }
        }

        $container->share(Engine::class, function () use ($project, $container, $intermediatePath) {
            $engine = Engine::create([$intermediatePath, $project->sourceDirectory], 'phtml');
            //$engine->register($container->get(Site::class));
            //$engine->register($container->get(Url::class));
            $engine->register($container->get(RenderProjectFile::class));
            //$engine->register($container->get(Helpers::class)); // @todo rewrite this for v4
            //$engine->register($container->get(Environment::class));

            return $engine;
        });
    }
}
