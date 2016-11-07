<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Step;

class Write implements Step
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Write constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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
        /** @var File $file */
        foreach ($project['files']->all() as $file) {
            if ($file->isDeferred()){ continue; }
            $output->writeln('[+] Writing File ['. $file->getUid() .'] to path ['. $file->getPermalink() .']');
            $this->filesystem->dumpFile($project->destinationDirectory . DIRECTORY_SEPARATOR . $file->getPermalink(), $file->getContent());
        }
    }
}
