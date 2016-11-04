<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Entities\FlatCollection;
use Tapestry\Entities\Generators\FileGenerator;
use Tapestry\Entities\Project;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;
use Tapestry\Modules\Renderers\ContentRendererFactory;
use Tapestry\Step;

class Compile implements Step
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array|File[]
     */
    private $files = [];

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
        /** @var ContentTypeFactory $contentTypes */
        $contentTypes = $project->get('content_types');

        /** @var ContentRendererFactory $contentRenderers */
        $contentRenderers = $project->get('content_renderers');

        // By this point the content type collections will be completed and it is safe to assume that we can now loop
        // over each content type and execute the generator on their items
        /** @var ContentType $contentType */
        foreach ($contentTypes->all() as $contentType) {
            $output->writeln('[+] Compiling content within ['. $contentType->getName() .']');

            // Foreach ContentType look up their Files and run the particular Renderer on their $content before updating
            // the Project File.
            foreach ($contentType->getFileList() as $fileKey) {
                /** @var File $file */
                if (! $file = $project->get('files.' . $fileKey)) {
                    continue;
                }

                // Pre-compile via use of File Generators
                if ($file instanceof FileGenerator){
                    $this->add($file->generate($project));
                }else{
                    $this->add($file);
                }
            }
        }

        foreach ($this->files as &$file) {
            $fileRenderer = $contentRenderers->get($file->getFileInfo()->getExtension());
            $file->setContent($fileRenderer->render($file));
            $file->setRendered(true);
            $file->setDeferred(false);
        }unset($file);

        $project->set('files', new FlatCollection($this->files));
        return true;
    }

    /**
     * @param File|File[] $files
     */
    private function add($files)
    {
        if (is_array($files)){
            foreach($files as $file) {
                $this->files[$file->getUid()] = $file;
            }
        }else{
            $this->files[$files->getUid()] = $files;
        }
    }
}
