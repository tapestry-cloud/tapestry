<?php

namespace Tapestry\Modules\Scripts;

use Tapestry\Step;
use Tapestry\Tapestry;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class After implements Step
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * Before constructor.
     *
     * @param Tapestry $tapestry
     */
    public function __construct(Tapestry $tapestry)
    {
        $this->tapestry = $tapestry;
    }

    /**
     * Process the Project at current.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return mixed
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $this->tapestry->getEventEmitter()->emit('scripts.after');

        return true;
    }
}
