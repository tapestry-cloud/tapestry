<?php

namespace Tapestry;

use ArrayAccess;
use League\Event\Emitter;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\ReflectionContainer;
use League\Container\ContainerAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Exceptions\InvalidConsoleInputException;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Tapestry\Exceptions\InvalidCurrentWorkingDirectoryException;

class Tapestry implements ContainerAwareInterface, ArrayAccess
{
    /**
     * The current globally available instance of Tapestry.
     *
     * @var static
     */
    protected static $instance;

    /**
     * @var \League\Container\ContainerInterface|\League\Container\Container
     */
    protected $container;

    /**
     * Version Number.
     *
     * @var string
     */
    const VERSION = '1.0.11';

    /**
     * Storage of data used by --stopwatch flag.
     *
     * @var Profiler
     */
    public static $profiler;

    /**
     * Tapestry constructor.
     *
     * InputInterface need only contain the command line options; this is because some of the service providers need to
     * know options such as --site-dir but do not need to know command arguments.
     *
     * @param InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        static::$profiler = new Profiler();

        $this->setInput($input);
        $this['events'] = new Emitter();
        $this->boot();
    }

    /**
     * @param array $options
     */
    private function parseOptions(array $options = [])
    {
        $this['cmd_options'] = $options;
        $this['environment'] = 'local';
        $this['currentWorkingDirectory'] = getcwd();

        if (isset($options['env'])) {
            $this['environment'] = $options['env'];
        }

        if (isset($options['site-dir'])) {
            $this['currentWorkingDirectory'] = $options['site-dir'];
        }

        $this['destinationDirectory'] = $this['currentWorkingDirectory'].DIRECTORY_SEPARATOR.'build_'.$this['environment'];

        if (isset($options['dist-dir'])) {
            $this['destinationDirectory'] = $options['dist-dir'];
        }
    }

    /**
     * @throws InvalidConsoleInputException
     */
    public function validateInput()
    {
        if (! file_exists($this['currentWorkingDirectory'])) {
            throw new InvalidCurrentWorkingDirectoryException('The site directory ['.$this['currentWorkingDirectory'].'] does not exist.');
        }

        if (! realpath($this['currentWorkingDirectory'])) {
            throw new InvalidConsoleInputException('There was an error while identifying the site directory, do you have read/write permissions?');
        }
    }

    /**
     * @param InputInterface $input
     * @throws InvalidConsoleInputException
     */
    public function setInput(InputInterface $input)
    {
        $this->parseOptions($input->getOptions());
        $this->getContainer()->add(InputInterface::class, $input);
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->getContainer()->add(OutputInterface::class, $output);
    }

    /**
     * Register/Boot Providers.
     *
     * @return void
     */
    public function boot()
    {
        $this->register(\Tapestry\Providers\CommandServiceProvider::class);
        $this->register(\Tapestry\Providers\ProjectConfigurationServiceProvider::class);
        $this->register(\Tapestry\Providers\ProjectServiceProvider::class);
        $this->register(\Tapestry\Providers\CompileStepsServiceProvider::class);
        $this->register(\Tapestry\Providers\PlatesServiceProvider::class);
        $this->register(\Tapestry\Providers\ProjectKernelServiceProvider::class);
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
            new ReflectionContainer()
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
        if (! isset($this->container)) {
            $this->setContainer(new Container());
        }

        return $this->container;
    }

    /**
     * Register a service provider.
     *
     * @param string|ServiceProviderInterface $serviceProvider
     *
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
    public static function setInstance(self $tapestry)
    {
        static::$instance = $tapestry;
    }

    public static function addProfile($name)
    {
        static::$profiler->addItem($name);
    }

    /**
     * @return Emitter
     */
    public function getEventEmitter()
    {
        return $this['events'];
    }

    /**
     * Whether a offset exists.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->getContainer()->has($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getContainer()->get($offset);
    }

    /**
     * Offset to set.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->getContainer()->add($offset, $value);
    }

    /**
     * Offset to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset
     *
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception("The container doesn't support removal of registered containers.");
    }
}
