<?php namespace Tapestry\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Tapestry\Entities\Configuration;

class Site implements ExtensionInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Site constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('site', [$this, 'site']);
    }

    public function site($key, $default = null)
    {
        $key = 'site.' . $key;
        if ($value = $this->configuration->get($key, $default)) {
            return $value;
        }
        return $default;
    }
}
