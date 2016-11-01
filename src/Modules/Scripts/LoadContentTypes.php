<?php namespace Tapestry\Modules\Scripts;

use Tapestry\Entities\Configuration;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\Project;
use Tapestry\Step;

class LoadContentTypes implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @return mixed
     */
    public function __invoke(Project $project)
    {
        /** @var Configuration $configuration */
        $configuration = $project->get('config');

        if (! $contentTypes = $configuration->get('content_types', null)) {
            $project->getOutput()->writeln('[!] Your project\'s content types are miss-configured. Doing nothing and exiting.]');
        }

        foreach ($contentTypes as $name => $settings)
        {
            $project->set('content_types.'. $name, new ContentType($name, $settings));
        }
    }
}
