<?php

namespace Tapestry\Entities\DependencyGraph;

interface Node
{
    public function getUid(): string;

    public function addEdge(Node $node);

    public function getEdges(): array;
}
