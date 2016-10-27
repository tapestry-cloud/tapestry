<?php namespace Tapestry\Modules\Scripts;

use Tapestry\Entities\Project;
use Tapestry\Step;
use Tapestry\Tapestry;

class Before implements Step
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    public function __construct(Tapestry $tapestry)
    {
        $this->tapestry = $tapestry;
    }

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
