<?php

namespace Tapestry\Entities;

class Tree
{
    /**
     * @var null|array
     */
    private $root = null;

    /**
     * Traverse all items within the tree and execute the callback for
     * each node in the tree.
     *
     * @param callable $callback
     * @param null|array $node
     */
    public function traverse(callable $callback, $node = null)
    {
        if (is_null($node)) {
            $this->traverse($this->root);
            return;
        }

        $callback($node);
        foreach($node['children'] as $child) {
            $this->traverse($callback, $child);
        }
    }

    /**
     * Add a new item to the tree.
     *
     * @param $item
     * @param $parent
     */
    public function add($item, $parent)
    {
        $newNode = [
            'value' => $item,
            'children' => []
        ];

        if (is_null($this->root)) {
            $this->root = $newNode;
            return;
        }

        $this->traverse(function($node) use ($parent, $newNode) {
            if ($node['value'] === $parent) {
                $node['children'][] = $newNode;
            }
        });
    }
}