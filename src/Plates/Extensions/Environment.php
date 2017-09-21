<?php

namespace Tapestry\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Tapestry\Entities\Project;

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
