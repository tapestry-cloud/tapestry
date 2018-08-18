<?php

namespace Tapestry\Entities\DependencyGraph;

class Debug
{
    /**
     * @var Graph
     */
    private $graph;

    private $styles = [];

    /**
     * @var string
     */
    private $name;

    /**
     * Debug constructor.
     * @param Graph $graph
     * @param array $styles
     * @param string $name
     */
    public function __construct(Graph $graph, array $styles = [], string $name = 'Tapestry')
    {
        $this->graph = $graph;
        $this->name = $name;

        if (count($styles) === 0) {
            $this->styles = [
                'rankdir=LR;',
                'bgcolor="0 0 .91";',
                'node [shape=rectangle];'
            ];
        } else {
            $this->styles = $styles;
        }
    }

    /**
     * @param string $edge
     * @param array|null $arr
     * @return string
     * @throws \Tapestry\Exceptions\GraphException
     */
    public function graphViz(string $edge, $arr = null): String
    {
        $arr = [
            sprintf('digraph %s {', $this->name),
        ];

        $walked = $this->walkGraph($edge, []);

        foreach ($this->styles as $style) {
            array_push($arr, sprintf('    %s', $style));
        } unset($style);

        $arr = array_merge($arr, $walked);
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
        if ($node instanceof SimpleNode) {
            array_push($this->styles, sprintf('"%s" [style="rounded,filled", fillcolor=yellow, shape=rectangle]', $node->getUid()));
        }

        foreach ($node->getEdges() as $edge) {
            if ($edge instanceof SimpleNode) {
                array_push($this->styles, sprintf('"%s" [style="rounded,filled", fillcolor=yellow, shape=rectangle]', $edge->getUid()));
            }

            $arr[] = sprintf('    "%s" -> "%s"', $node->getUid(), $edge->getUid());
            if (count($edge->getEdges()) > 0) {
                $arr = $this->walkGraph($edge->getUid(), $arr);
            }
        }

        return $arr;
    }
}
