<?php

namespace Tapestry\Modules\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension;
use Tapestry\Entities\Configuration;

/**
 * Class Site.
 *
 * The Site extension to Plates provides the user with the `site` method.
 * This allows you to get site configuration by key.
 */
class Site implements Extension
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Site constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Register the `site` helper with Plates.
     *
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->addMethods(['site' => [$this, 'site']]);
    }

    /**
     * Return site config by `$key` or return `$default` if not found.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function site(string $key, $default = null)
    {
        $key = 'site.'.$key;
        if ($value = $this->configuration->get($key, $default)) {
            return $value;
        }

        return $default;
    }
}
