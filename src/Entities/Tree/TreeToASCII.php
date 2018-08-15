<?php

namespace Tapestry\Entities\Tree;

class TreeToASCII
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * TreeToASCII constructor.
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function __toString()
    {
        $output = '';
        $this->tree->traverse(function (Leaf $leaf, Leaf $parent = null, $depth) use (&$output) {
            $ascii = '├──';

            if (! is_null($parent)) {
                /** @var Leaf $last */
                $c = $parent->getChildren();
                $last = end($c);
                if ($last->getId() === $leaf->getId()) {
                    $ascii = '└──';
                }
                unset($c, $last);
            } else {
                $ascii = '└──';
            }

            $pad = '';
            for ($i = 0; $i < $depth * 4; $i++) {
                $pad .= ' ';
            }

            $output .= $pad.$ascii.$leaf->getSymbol()->id.PHP_EOL;
        });

        return $output;
    }
}
