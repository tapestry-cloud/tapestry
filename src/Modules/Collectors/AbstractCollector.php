<?php

namespace Tapestry\Modules\Collectors;

use Tapestry\Modules\Collectors\Exclusions\ExclusionInterface;
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
     * @var array|ExclusionInterface[]
     */
    private $filterCollection;

    /**
     * AbstractCollector constructor.
     *
     * @param string $name
     * @param array $mutatorCollection
     * @param array $filterCollection
     */
    public function __construct(string $name, array $mutatorCollection = [], array $filterCollection = [])
    {
        $this->name = $name;
        $this->mutatorCollection = $mutatorCollection;
        $this->filterCollection = $filterCollection;
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
        // @todo implement defaultData as mutators that this iterates over
        foreach($this->mutatorCollection as $mutator) {
            $mutator->mutate($source);
        }
        return $source;
    }

    /**
     * @param array|SourceInterface[] $collection
     * @return array|SourceInterface[]
     */
    protected function filterCollection(array $collection)
    {
        return array_filter($collection, function(SourceInterface $el){
            foreach ($this->filterCollection as $filter) {
                if ($filter->filter($el) === true) {
                    return false;
                }
            }
            return true;
        });
    }
}