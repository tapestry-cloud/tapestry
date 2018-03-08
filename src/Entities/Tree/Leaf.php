<?php

namespace Tapestry\Entities\Tree;

class Leaf
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var mixed
     */
    private $entity;

    /**
     * @var array|Leaf[]
     */
    private $children = [];

    /**
     * Leaf constructor.
     * @param string $id
     * @param mixed $entity
     */
    public function __construct(string $id, $entity)
    {
        $this->id = $id;
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Leaf $entity
     */
    public function addChild(Leaf $entity)
    {
        $this->children[] = $entity;
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
}