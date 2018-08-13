<?php

namespace Tapestry\Steps;

use Tapestry\Entities\DependencyGraph\Debug;
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

        $graph = $project->getGraph();

        foreach($project->allSources() as $source)
        {
            if ($source->isToCopy()) {
                continue;
            }

            // @todo needs to identify phtml layout's for the AST tree.
            if ($template = $source->getData('template')) {
                if (strpos($template, '.') === false) {
                    $template .= '.phtml';
                }
                $graph->addEdge($this->templateUid($template), $graph->getEdge($source->getUid()));
            }

            // Plates v4 uses $v->layout and $v->insert to define dependencies
            if ($source->getExtension() === 'phtml') {
                $tokens = token_get_all($source->getRenderedContent());
                foreach ($tokens as $k => $token) {
                    if($token[0] === T_VARIABLE && $token[1] === '$v'){
                        if ($tokens[$k+1][0] === T_OBJECT_OPERATOR){
                            if ($tokens[$k+2][0] === T_STRING && ($tokens[$k+2][1] === 'layout' || $tokens[$k+2][1] === 'insert')){
                                if ($tokens[$k+3] === '(' && $tokens[$k+4][0] === T_CONSTANT_ENCAPSED_STRING){
                                    $found = substr($tokens[$k+4][1], 1, -1);
                                    if (strpos($found, '.phtml') === false) {
                                        $found .= '.phtml';
                                    }
                                    $graph->addEdge($this->templateUid($found), $graph->getEdge($source->getUid()));
                                }
                            }
                        }
                    }
                }
            }
        }

        // @todo reduce the graph and provide the compile steps with a list of source files that are queued for compilation
        // @todo write debug export of graph to graphviz format.

        //$p = (new TreeToASCII($tree))->__toString();

        $debug = new Debug($graph);
        $n = $debug->graphViz('kernel');

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
