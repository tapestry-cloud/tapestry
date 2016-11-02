<?php namespace Tapestry\Modules\ContentTypes;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Step;

class ParseContentTypes implements Step
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
        /** @var ContentTypeFactory $contentTypes */
        $contentTypes = $project->get('content_types');

        /** @var File $file */
        foreach($project['files'] as $file) {
            if (! $contentType = $contentTypes->find($file->getFileInfo()->getRelativePath())){
                $contentType = $contentTypes->get('*');
            }else{
                $contentType = $contentTypes->get($contentType);
            }

            $contentType->addFile($file);

            $output->writeln('[+] File ['. $file->getFileInfo()->getRelativePathname() .'] bucketed into content type ['. $contentType->getName() .']');
        }
    }
}
