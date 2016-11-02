<?php namespace Tapestry\Modules\Content;

use Tapestry\Entities\Project;
use Tapestry\Step;

class LoadSourceFiles implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @return mixed
     */
    public function __invoke(Project $project)
    {
        $sourcePath = $project->get('cwd') . DIRECTORY_SEPARATOR . 'source';

        if (! file_exists($sourcePath)){
            $project->getOutput()->writeln('[!] The project source path could not be opened at ['.$sourcePath.']');
            return false;
        }

        // TODO: Implement __invoke() method.
    }
}
