<?php

namespace Tapestry\Modules\Collectors;

use Tapestry\Modules\Collectors\Mutators\MutatorInterface;
use Tapestry\Modules\Source\SourceInterface;

abstract class AbstractCollector implements CollectorInterface
{

    /**
     * Collector Name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * @var array|MutatorInterface[]
     */
    private $mutatorCollection;

    /**
     * AbstractCollector constructor.
     *
     * @param string $name
     * @param array $mutatorCollection
     */
    public function __construct(string $name, array $mutatorCollection = [])
    {
        $this->name = $name;
        $this->mutatorCollection = $mutatorCollection;
    }

    /**
     * @return string
     */
    public function getName(): String
    {
        return $this->name;
    }

    /**
     * Iterate over this collectors mutator collection and allow each to
     * mutate the SourceInterface.
     *
     * @param SourceInterface $source
     * @return SourceInterface
     */
    protected function mutateSource(SourceInterface $source) : SourceInterface
    {
        // @todo implement isDraft, isIgnored, defaultData, etc as mutators that this iterates over
        foreach($this->mutatorCollection as $mutator) {
            $mutator->mutate($source);
        }
        return $source;
    }

}