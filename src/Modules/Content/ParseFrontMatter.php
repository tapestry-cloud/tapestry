<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Step;

class ParseFrontMatter implements Step
{
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
        foreach ($project['files'] as $file){
            $output->writeln('[+] Parsing FrontMatter for ['. $file->getFileInfo()->getRelativePathname() .']');
            $frontMatter = new FrontMatter($file->getFileContent());
            $file->setData($frontMatter->getData());
            $file->setContent($frontMatter->getContent());
        }

        return true;
    }
}
