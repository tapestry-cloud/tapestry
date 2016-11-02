<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Step;

class ParseFrontMatter implements Step
{
    /**
     * @var string
     */
    private $pattern = '/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s';

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
        }
    }
}
