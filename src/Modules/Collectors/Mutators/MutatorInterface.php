<?php

namespace Tapestry\Modules\Collectors\Mutators;

use Tapestry\Modules\Source\SourceInterface;

interface MutatorInterface
{

    public function mutate(SourceInterface &$source);

}