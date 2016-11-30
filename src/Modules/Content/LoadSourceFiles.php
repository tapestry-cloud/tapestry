<?php

namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Collections\ExcludedFilesCollection;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;
use Tapestry\Modules\Renderers\ContentRendererFactory;
use Tapestry\Step;
use Tapestry\Tapestry;

class LoadSourceFiles implements Step
{
    /**
     * @var Tapestry
     */
    private $tapestry;

    /**
     * @var ExcludedFilesCollection
     */
    private $excluded;

    /**
     * @var bool
     */
    private $prettyPermalink = true;

    private $publishDrafts = false;

    /**
     * LoadSourceFiles constructor.
     *
     * @param Tapestry      $tapestry
     * @param Configuration $configuration
     */
    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->tapestry = $tapestry;
        $this->excluded = new ExcludedFilesCollection($configuration->get('ignore'));
        $this->prettyPermalink = boolval($configuration->get('pretty_permalink', true));
        $this->publishDrafts = boolval($configuration->get('publish_drafts', false));
    }

    /**
     * Process the Project at current.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $sourcePath = $project->sourceDirectory;

        if (! file_exists($sourcePath)) {
            $output->writeln('[!] The project source path could not be opened at ['.$sourcePath.']');

            return false;
        }

        /** @var ContentTypeFactory $contentTypes */
        $contentTypes = $project->get('content_types');

        /** @var ContentRendererFactory $contentRenderers */
        $contentRenderers = $project->get('content_renderers');

        $finder = new Finder();
        $finder->files()
            ->followLinks()
            ->in($project->sourceDirectory)
            ->exclude(['_views', '_templates'])
            ->ignoreDotFiles(true);

        $this->excluded->excludeFromFinder($finder);

        foreach ($finder->files() as $file) {
            $file = new File($file, [
                'pretty_permalink' => $this->prettyPermalink,
            ]);
            $renderer = $contentRenderers->get($file->getFileInfo()->getExtension());

            if ($renderer->supportsFrontMatter()) {
                $frontMatter = new FrontMatter($file->getFileContent());
                $file->setData($frontMatter->getData());
                $file->setContent($frontMatter->getContent());
            }

            if ($this->publishDrafts === false) {
                if (boolval($file->getData('draft', false)) === true) {
                    continue;
                }
            }

            if (! $contentType = $contentTypes->find($file->getFileInfo()->getRelativePath())) {
                $contentType = $contentTypes->get('*');
            } else {
                $contentType = $contentTypes->get($contentType);
            }

            $contentType->addFile($file);
            $project->addFile($file);

            $output->writeln('[+] File ['.$file->getFileInfo()->getRelativePathname().'] bucketed into content type ['.$contentType->getName().']');
        }

        $output->writeln('[+] Discovered ['.$project['files']->count().'] project files');

        return true;
    }
}
