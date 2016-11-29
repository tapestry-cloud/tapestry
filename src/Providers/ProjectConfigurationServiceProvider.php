<?php

namespace Tapestry\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Tapestry\Entities\Configuration;
use Tapestry\Tapestry;

class ProjectConfigurationServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Configuration::class,
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

        /** @var Tapestry $tapestry */
        $tapestry = $container->get(Tapestry::class);

        $configuration = new Configuration(include(__DIR__.'/../../src/Modules/Config/DefaultConfig.php'));

        $configPath = $tapestry['currentWorkingDirectory'].DIRECTORY_SEPARATOR.'config.php';
        if (file_exists($configPath)) {
            $configuration->merge(include($configPath));
        }

        $configPath = $tapestry['currentWorkingDirectory'].DIRECTORY_SEPARATOR.'config-'.$tapestry['environment'].'.php';
        if (file_exists($configPath)) {
            $configuration->merge(include($configPath));
        }

        $container->share(Configuration::class, $configuration);
    }
}
