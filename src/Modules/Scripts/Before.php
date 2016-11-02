<?php namespace Tapestry\Modules\Scripts;

use League\Event\Event;
use Symfony\Component\Console\Output\OutputInterface;
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
     * @param OutputInterface $output
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $this->tapestry->getEventEmitter()->emit('scripts.before');
        return true;
    }
}
