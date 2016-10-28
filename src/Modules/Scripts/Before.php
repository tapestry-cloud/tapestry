<?php namespace Tapestry\Modules\Scripts;

use League\Event\Event;
use Tapestry\Entities\Project;
use Tapestry\Step;
use Tapestry\Tapestry;

class Before implements Step
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
        $this->tapestry->getEventEmitter()->emit('scripts.before');
        return true;
    }
}
