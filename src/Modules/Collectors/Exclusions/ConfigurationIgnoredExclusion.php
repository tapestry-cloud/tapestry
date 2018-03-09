<?php

namespace Tapestry\Modules\Collectors\Exclusions;

use Tapestry\Entities\Configuration;

/**
 * Class ConfigurationIgnoredExclusion.
 *
 * Filters out files with a path matching any set within the ignored configuration array.
 */
class ConfigurationIgnoredExclusion extends ArrayPathExclusion implements ExclusionInterface
{
    /**
     * DraftsExclusion constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration->get('ignored', []));
    }
}
