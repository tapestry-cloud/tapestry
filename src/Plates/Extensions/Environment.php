<?php

namespace Tapestry\Plates\Extensions;

use League\Plates\Engine;
use Tapestry\Entities\Project;
use League\Plates\Extension\ExtensionInterface;

class Environment implements ExtensionInterface
{
    /**
     * @var string
     */
    private $environment = '';

    /**
     * Environment constructor.
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->environment = $project->environment;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('getEnvironment', [$this, 'getEnvironment']);
    }

    public function getEnvironment()
    {
        return $this->environment;
    }
}
