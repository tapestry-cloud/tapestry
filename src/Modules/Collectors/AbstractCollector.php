<?php

namespace Tapestry\Modules\Collectors;

abstract class AbstractCollector implements CollectorInterface
{

    /**
     * Collector Name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * AbstractCollector constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): String
    {
        return $this->name;
    }

}