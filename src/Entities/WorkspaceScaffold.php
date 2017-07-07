<?php

namespace Tapestry\Entities;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\WorkspaceScaffold\Step;

class WorkspaceScaffold
{
    /**
     * Name of the Scaffold, should contain no spaces as this is how the user will
     * reference the scaffold from the command line via make:scaffold-name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Short description of the scaffold, this will be output to the console
     * as a list of available scaffolds when the make command has no
     * scaffold name provided.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Marks if the scaffold is finished or not. Can be set by a Step through
     * the Complete() method.
     *
     * @var bool
     */
    protected $isComplete = false;

    /**
     * Current step pointer, should equal a valid key in the $steps map
     *
     * @var null|string
     */
    protected $step = null;

    /**
     * Map of steps.
     *
     * @var \Tapestry\Entities\WorkspaceScaffold\Step[]
     */
    protected $steps = [];

    /**
     * @var array
     */
    protected $initialModel = [];

    /**
     * The Model is a basic array by default, but there is no reason why you can't have it be
     * a Collection or anything you want. This has been kept intentionally ambiguous so that
     * the end developer can do what they are comfortable with/what works in the given
     * context.
     *
     * @var array
     */
    protected $model = [];

    /**
     * WorkspaceScaffold constructor.
     *
     * @param string $name
     * @param string $description
     * @param array $steps
     * @param array $model
     * @param array $validator
     */
    public function __construct($name = '', $description = '', $steps = [], $model = [], $validator = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->steps = $steps;
        $this->initialModel = $model;
        $this->validator = $validator;

        $this->reset();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function complete()
    {
        $this->isComplete = true;
    }

    /**
     * Reset the step pointer to the beginning of the steps map.
     */
    public function reset()
    {
        $this->isComplete = false;
        $this->model = $this->initialModel;
        $keys = array_keys($this->steps);
        $this->step = array_shift($keys);
    }

    /**
     * @param OutputInterface $output
     * @return bool
     * @throws \Exception
     */
    public function execute(OutputInterface $output)
    {
        if (is_null($this->step)) {
            return false;
        }

        if (! isset($this->steps[$this->step])) {
            return false;
        }

        /** @var \Tapestry\Entities\WorkspaceScaffold\Step $current */
        $current = $this->steps[$this->step];
        if ($current instanceof Step) {
            throw new \Exception('All workspace scaffold steps must be instances of \Tapestry\Entities\WorkspaceScaffold\Step.');
        }

        $result = $current($output, $this);
        if (!is_bool($result)) {
            throw new \Exception('The result of your workspace scaffold step must be boolean.');
        }

        return $result;
    }
}