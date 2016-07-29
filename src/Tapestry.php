<?php namespace Tapestry;

use ArrayAccess;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use League\Container\ReflectionContainer;
use League\Container\Container;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Symfony\Component\Console\Input\ArgvInput;

class Tapestry implements ContainerAwareInterface, ArrayAccess
{
    /**
     * The current globally available instance of Tapestry
     *
     * @var static
     */
    protected static $instance;

    /**
     * @var \League\Container\ContainerInterface|\League\Container\Container
     */
    protected $container;

    /**
     * Version Number
     * @var string
     */
    const VERSION = '0.0.1';

    /**
     * Tapestry constructor.
     * @param array $arguments
     */
    public function __construct($arguments = [])
    {
        if (php_sapi_name() === 'cli') {
            $input = new ArgvInput();
            if ((!$siteEnvironment = $input->getParameterOption('--env')) && (!$siteEnvironment = $input->getParameterOption('-e'))) {
                $siteEnvironment = 'local';
            }
            if (!$siteDirectory = $input->getParameterOption('--site-dir')) {
                $siteDirectory = getcwd();
            }
        }else{
            $siteEnvironment = (isset($arguments['environment'])) ? $arguments['environment'] : 'local';
            $siteDirectory = (isset($arguments['cwd'])) ? $arguments['cwd'] : getcwd();
        }

        $this['environment'] = $siteEnvironment;
        $this['currentWorkingDirectory'] = $siteDirectory;
    }

    /**
     * Set a container.
     *
     * @param \League\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        $this->container->delegate(
            new ReflectionContainer
        );
        $this->container->add(self::class, $this);
        self::setInstance($this);
    }

    /**
     * Get the container.
     *
     * @return \League\Container\ContainerInterface
     */
    public function getContainer()
    {
        if (!isset($this->container)) {
            $this->setContainer(new Container);
        }
        return $this->container;
    }

    /**
     * Register a service provider
     *
     * @param  string|ServiceProviderInterface $serviceProvider
     * @return void
     */
    public function register($serviceProvider)
    {
        $this->getContainer()->addServiceProvider($serviceProvider);
    }


    /**
     * @return Tapestry
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * @param Tapestry $tapestry
     */
    public static function setInstance(Tapestry $tapestry)
    {
        static::$instance = $tapestry;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->getContainer()->has($offset);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getContainer()->get($offset);
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->getContainer()->add($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception("The container doesn't support removal of registered containers.");
    }
}

