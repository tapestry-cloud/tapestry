<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Collection;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\File;
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
     * @param Configuration $configuration
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
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $sourcePath = $project->sourceDirectory;

        if (! file_exists($sourcePath)){
            $output->writeln('[!] The project source path could not be opened at ['.$sourcePath.']');
            return false;
        }

        $finder = new Finder();
        $finder->files()
            ->followLinks()
            ->in($project->sourceDirectory)
            ->ignoreDotFiles(true);

        // todo add exclusions

        foreach($finder->files() as $file)
        {
            $project->addFile(new File($file));
        }

        return true;
    }
}
