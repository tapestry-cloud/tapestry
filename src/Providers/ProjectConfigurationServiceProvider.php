<?php

namespace Tapestry\Providers;

use Symfony\Component\Yaml\Yaml;
use Tapestry\Tapestry;
use Tapestry\Entities\Configuration;
use League\Container\ServiceProvider\AbstractServiceProvider;

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

        if ($baseConfigPathName = $this->identifyConfigurationPath($tapestry['currentWorkingDirectory'])) {
            $configuration->merge($this->getConfigurationFromPath($baseConfigPathName));
        }

        if ($envConfigPathName = $this->identifyConfigurationPath($tapestry['currentWorkingDirectory'], $tapestry['environment'])) {
            $configuration->merge($this->getConfigurationFromPath($envConfigPathName));
        }

        $container->share(Configuration::class, $configuration);
    }

    private function identifyConfigurationPath($configPath, $env = null) {
        $basePath = $configPath . DIRECTORY_SEPARATOR . 'config'. (is_null($env) ? '' : ('-' . $env));
        $PHPPath = $basePath . '.php';
        $YAMLPath = $basePath . '.yaml';
        $configPHPExists = file_exists($PHPPath);
        $configYAMLExists = file_exists($YAMLPath);
        if ($configPHPExists && $configYAMLExists) {
            throw new \Exception('Configuration can only be either PHP or YAML based, not both.');
        }
        if ($configPHPExists) {
            return $PHPPath;
        }
        if ($configYAMLExists) {
            return $YAMLPath;
        }
        return null;
    }

    private function getConfigurationFromPath($path) {
        if (strpos($path, 'php') !== false) {
            return include($path);
        }
        if (strpos($path, 'yaml') !== false) {
            return Yaml::parse(file_get_contents($path));
        }
    }
}
