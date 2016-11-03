<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Step;

class Compile implements Step
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
        // By this point the content type collections will be completed and it is safe to assume that we can now loop
        // over each content type and execute the generator on their items
        /** @var ContentType $contentType */
        foreach ($project['content_types']->all() as $contentType) {
            $output->writeln('[+] Compiling content within ['. $contentType->getName() .']');

            // Foreach ContentType look up their Files and run the particular Renderer on their $content before updating
            // the Project File.
            foreach ($contentType->getFileList() as $fileKey) {
                if (! $file = $project->get('files.' . $fileKey)) {
                    continue;
                }
                $n = 1;
            }
        }

        $n = 1;
    }
}
