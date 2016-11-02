<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Step;
use Tapestry\Tapestry;

class LoadSourceFiles implements Step
{

    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * LoadSourceFiles constructor.
     * @param Tapestry $tapestry
     */
    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->tapestry = $tapestry;
    }

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     * @return mixed
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $sourcePath = $project->sourceDirectory;

        if (! file_exists($sourcePath)){
            $output->writeln('[!] The project source path could not be opened at ['.$sourcePath.']');
            return false;
        }

        dd($project->get('content_types.blog'));


        // TODO: Implement __invoke() method.
    }
}
