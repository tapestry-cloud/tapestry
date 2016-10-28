<?php namespace Tapestry\Modules\Scripts;

use Tapestry\Entities\Project;
use Tapestry\Step;
use Tapestry\Tapestry;

class After implements Step
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * Before constructor.
     * @param Tapestry $tapestry
     */
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
        $this->tapestry->getEventEmitter()->emit('scripts.after');
        return true;
    }
}
