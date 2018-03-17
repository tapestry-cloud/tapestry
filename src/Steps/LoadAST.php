<?php

namespace Tapestry\Steps;

use Tapestry\Entities\Configuration;
use Tapestry\Entities\Tree\Leaf;
use Tapestry\Entities\Tree\Symbol;
use Tapestry\Entities\Tree\Tree;
use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Tapestry;

/**
 * Class LoadAST
 *
 * This Step initiates the AST Tree structure and assigns it to the Project Container.
 */
class LoadAST implements Step
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * LoadContentGenerators constructor.
     *
     * @param Tapestry $tapestry
     * @param Configuration $configuration
     */
    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Exception
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $tree = $project->getAST();

        $configurationSymbol = new Symbol('configuration', Symbol::SYMBOL_CONFIGURATION, -1);
        $configurationSymbol->setHash(sha1(json_encode($this->configuration->all())));

        $tree->add(new Leaf('configuration', $configurationSymbol), 'kernel');

        return true;
    }
}
