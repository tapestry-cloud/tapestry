<?php

namespace Tapestry\Modules\Plates\Extensions;

use League\Plates\Engine;
use Tapestry\Entities\Project;
use League\Plates\Extension;

/**
 * Class Environment
 *
 * The Environment extension to Plates provides the user the `getEnvironment` method.
 * This allows you to get the current environment as set via the `--env` flag from
 * the command line, from within a template.
 *
 * @package Tapestry\Modules\Plates\Extensions
 */
class Environment implements Extension
{
    /**
     * The current environment as set via `--env` flag from command line.
     *
     * @var string
     */
    private $environment = '';

    /**
     * Environment constructor.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->environment = $project->environment;
    }

    /**
     * Register the `getEnvironment` helper with Plates.
     *
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->addMethods(['getEnvironment' => [$this, 'getEnvironment']]);
    }

    /**
     * Returns the current environment.
     *
     * @return string
     */
    public function getEnvironment() : string
    {
        return $this->environment;
    }
}
