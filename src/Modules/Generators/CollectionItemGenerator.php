<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\DependencyGraph\Cluster;
use Tapestry\Entities\DependencyGraph\SimpleNode;
use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Modules\Source\SourceInterface;

class CollectionItemGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * Run the generation and return an array of generated
     * files (oddly implementing SourceInterface, naming
     * things is hard!)
     *
     * This generator mutates the Source file and injects computed previous_next.
     *
     * @param Project $project
     * @return array|SourceInterface[]
     * @throws \Exception
     */
    public function generate(Project $project): array
    {
        $nodeId = $this->source->getUid().'_generator_CollectionItemGenerator_'.time();
        $project->addSource($this->source->getUid(), new SimpleNode($nodeId, 'CollectionItemGenerator'));

        // @todo the previous version of this generator cloned $this->source. Is there a reason for that?

        // Remove reference to this Generator from source (otherwise it will execute again forever)
        $this->source->setData('generator', array_filter($this->source->getData('generator', []), function ($v) {
            return $v !== 'CollectionItemGenerator';
        }));

        // Identify Previous and Next Item within this files collection
        if ($contentType = $this->source->getData('content_type')) {
            $contentType = $project->getContentType($contentType);
        }

        // @todo if the source file doesn't have content_type set or the content_type it returns isn't registered with $project then this will error as $contetnType will equal null
        $siblings = array_keys($contentType->getSourceList('asc'));
        $position = array_search($this->source->getUid(), $siblings);

        if (($position === (count($siblings) - 1))) {
            // If we are the last page, then Pagination will only have two pages (this and the previous one), also there will
            // be just two files in the items array

            $pagination = new Pagination([], 2, 2);
        } elseif ($position === 0) {
            // If we are the first page, then Pagination will only have two pages (this and the next one), also there will be
            // just two files in the items array

            $pagination = new Pagination([], 2, 1);
        } else {
            // Else this is the middle page of a total of three (previous, this, next)

            $pagination = new Pagination([], 3, 2);
        }

        $previous = (isset($siblings[$position - 1])) ? $project->getSource($siblings[$position - 1]) : null;
        $next = (isset($siblings[$position + 1])) ? $project->getSource($siblings[$position + 1]) : null;

        $pagination->setPreviousNext($previous, $next);

        // Add dependencies to graph.

        if (! is_null($previous)) {
            $cluster = new Cluster($nodeId . '_previous', [$previous]);
            $project->getGraph()->addEdge($nodeId, $cluster);
            $project->getGraph()->addEdge($previous->getUid(),$cluster);
        } unset($cluster);

        if (! is_null($next)) {
            $cluster = new Cluster($nodeId . '_next', [$next]);
            $project->getGraph()->addEdge($nodeId, $cluster);
            $project->getGraph()->addEdge($next->getUid(),$cluster);
        } unset($cluster);

        $this->source->setData(['previous_next' => $pagination]);

        return [$this->source];
    }
}