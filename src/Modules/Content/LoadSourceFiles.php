<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;
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

        /** @var ContentTypeFactory $contentTypes */
        $contentTypes = $project->get('content_types');

        $finder = new Finder();
        $finder->files()
            ->followLinks()
            ->in($project->sourceDirectory)
            ->exclude(['_views','_templates'])
            ->ignoreDotFiles(true);

        // todo add configured exclusions

        foreach($finder->files() as $file)
        {
            $file = new File($file);

            // @todo Identify the files renderer

            // @todo only load FrontMatter if the files renderer supports it
            $frontMatter = new FrontMatter($file->getFileContent());
            $file->setData($frontMatter->getData());
            $file->setContent($frontMatter->getContent());

            if (! $contentType = $contentTypes->find($file->getFileInfo()->getRelativePath())){
                $contentType = $contentTypes->get('*');
            }else{
                $contentType = $contentTypes->get($contentType);
            }

            $contentType->addFile($file);
            $project->addFile($file);

            $output->writeln('[+] File ['. $file->getFileInfo()->getRelativePathname() .'] bucketed into content type ['. $contentType->getName() .']');
        }

        $output->writeln('[+] Discovered ['. $project['files']->count() .'] project files');

        return true;
    }
}
