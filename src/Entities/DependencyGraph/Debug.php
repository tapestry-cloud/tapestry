<?php

namespace Tapestry\Entities\DependencyGraph;


class Debug
{
    /**
     * @var Graph
     */
    private $graph;

    /**
     * Debug constructor.
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * @param string $edge
     * @param array|null $arr
     * @return String
     * @throws \Tapestry\Exceptions\GraphException
     */
    public function graphViz(string $edge, $arr = null): String
    {
        if (is_null($arr)) {
            $arr = [
                'digraph Tapestry {',
                '    rankdir=LR;',
                '    bgcolor="0 0 .91";',
                '    node [shape=circle];',
            ];
        }

        $arr = array_merge($arr, $this->walkGraph($edge, []));

        $arr[] = '}';

        return implode(PHP_EOL, $arr);
    }

    /**
     * @param string $edge
     * @param array $arr
     * @return array
     * @throws \Tapestry\Exceptions\GraphException
     */
    private function walkGraph(string $edge, array $arr): array
    {
        $node = $this->graph->getEdge($edge);
        foreach ($node->getEdges() as $edge){
            $arr[] = sprintf('    "%s" -> "%s"', $node->getUid(), $edge->getUid());
            if (count($edge->getEdges()) > 0) {
                $arr = $this->walkGraph($edge->getUid(), $arr);
            }
        }

        return $arr;
    }
}