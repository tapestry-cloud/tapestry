<?php

namespace Tapestry\Steps;

use League\Plates\Engine;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Entities\ProjectFile;
use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;

class RenderPlates implements Step
{
    /**
     * @var Engine
     */
    private $plates;

    /**
     * RenderPlates constructor.
     * @param Engine $plates
     */
    public function __construct(Engine $plates)
    {
        $this->plates = $plates;
    }

    /**
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        /** @var FlatCollection $files */
        $files = $project['files'];

        foreach ($project->get('plates_cache') as $id => $path) {
            if (substr($path, 0 ,1) === '/') {
                $path = substr($path, 1);
            }

            $p = explode('.', $path);
            array_pop($p);

            $path = implode('/', $p);

            /** @var ProjectFile $file */
            $file = $files->get($id);

            $html = $this->plates->render($path, ['projectFile' => $file]);
        }

        return true;
    }
}
