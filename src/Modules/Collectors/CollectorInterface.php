<?php

namespace Tapestry\Modules\Collectors;

interface CollectorInterface
{
    public function getName(): string;

    /**
     * @return array|\Tapestry\Modules\Source\SourceInterface[]
     */
    public function collect(): array;
}
