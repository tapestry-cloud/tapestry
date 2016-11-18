<?php namespace Tapestry\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Tapestry\Console\Application;
use Tapestry\Console\Commands\BuildCommand;
use Tapestry\Console\Commands\InitCommand;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Plates\Engine;
use Tapestry\Plates\Extensions\Site;
use Tapestry\Plates\Extensions\Url;
use Tapestry\Tapestry;

class ProjectConfigurationServiceProvider extends AbstractServiceProvider
{

    /**
     * @var array
     */
    protected $provides = [
        Configuration::class
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
        /** @var Tapestry $tapestry */
        $tapestry = $this->getContainer()->get(Tapestry::class);

        $configuration = new Configuration(include(__DIR__ . '/../../src/Modules/Config/DefaultConfig.php'));

        $configPath = $tapestry['currentWorkingDirectory'] . DIRECTORY_SEPARATOR . 'config.php';

        if (file_exists($configPath)){
            $configuration->merge(include($configPath));
        }

        $this->getContainer()->share(Configuration::class, $configuration);
    }
}