<?php namespace Tapestry\Modules\Content;

use Tapestry\Entities\Project;
use Tapestry\Step;

class ParseFrontMatter implements Step
{
    /**
     * @var string
     */
    private $pattern = '/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s';

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @return mixed
     */
    public function __invoke(Project $project)
    {
        // TODO: Implement __invoke() method.
    }
}
