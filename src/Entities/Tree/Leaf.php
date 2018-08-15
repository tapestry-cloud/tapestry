<?php

namespace Tapestry\Entities\Tree;

/**
 * Class Leaf.
 *
 * Leaf node container class for the Tree data structure.
 */
class Leaf
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Symbol
     */
    private $symbol;

    /**
     * @var array|Leaf[]
     */
    private $children = [];

    /**
     * @var bool
     */
    private $hasChildren = false;

    /**
     * Leaf constructor.
     *
     * @param string $id
     * @param Symbol  $symbol
     */
    public function __construct(string $id, Symbol $symbol)
    {
        $this->id = $id;
        $this->symbol = $symbol;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Symbol
     */
    public function getSymbol(): Symbol
    {
        return $this->symbol;
    }

    /**
     * @param Leaf $entity
     * @return void
     */
    public function addChild(self $entity)
    {
        $this->hasChildren = true;
        $this->children[$entity->getId()] = $entity;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->hasChildren;
    }

    /**
     * @return int
     */
    public function childCount(): int
    {
        return count($this->children);
    }

    /**
     * @return array|Leaf[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param string $id
     * @return Leaf
     */
    public function getChild(string $id): Leaf
    {
        return $this->children[$id];
    }
}
