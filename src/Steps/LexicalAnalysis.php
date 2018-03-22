<?php

namespace Tapestry\Steps;

use Tapestry\Entities\Tree\Leaf;
use Tapestry\Entities\Tree\Symbol;
use Tapestry\Entities\Tree\TreeToASCII;
use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class LexicalAnalysis implements Step
{
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
        //
        // For speed other Steps take part in the LexicalAnalysis and building the AST Tree.
        // The role of this Step is to complete the job so that the Compilation steps can
        // focus on only dealing with Tree nodes that have changed since the last run.
        //

        $tree = $project->getAST();

        foreach($project->allSources() as $source)
        {
            if ($source->isToCopy() || $source->isIgnored()) {
                continue;
            }

            // @todo needs to identify phtml layout's for the AST tree.
            if ($template = $source->getData('template')) {
                if (strpos($template, '.') === false) {
                    $template .= '.phtml';
                }
                $tree->add(new Leaf($source->getUid(), new Symbol($source->getUid(), Symbol::SYMBOL_SOURCE, $source->getMTime())), $this->templateUid($template));
            }
        }

        // @todo reduce the AST and provide the compile steps with a list of source files that are queued for compilation

        $p = (new TreeToASCII($tree))->__toString();

        return true;
    }

    /**
     * Identify the Source UID for this ContentType's template.
     *
     * @return string
     */
    private function templateUid(string $template): string
    {
        $uid = str_replace('.', '_', $template);
        $uid = str_replace(['/', '\\'], '_', $uid);
        return $uid;
    }
}
