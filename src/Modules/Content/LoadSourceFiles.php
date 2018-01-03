<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Tapestry;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Configuration;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;
use Tapestry\Modules\Renderers\ContentRendererFactory;
use Tapestry\Entities\Collections\ExcludedFilesCollection;

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

    /**
     * @var bool
     */
    private $publishDrafts = false;

    /**
     * --auto-publish option from cli.
     * @var bool
     */
    private $autoPublish = false;

    /**
     * @var \DateTime
     */
    private $now;

    /**
     * LoadSourceFiles constructor.
     *
     * @param Tapestry      $tapestry
     * @param Configuration $configuration
     */
    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->tapestry = $tapestry;
        $this->now = new \DateTime();
        $this->excluded = new ExcludedFilesCollection(array_merge($configuration->get('ignore'), ['_views', '_templates']));
        $this->prettyPermalink = boolval($configuration->get('pretty_permalink', true));
        $this->publishDrafts = boolval($configuration->get('publish_drafts', false));
        $this->autoPublish = (isset($tapestry['cmd_options']['auto-publish']) ? boolval($tapestry['cmd_options']['auto-publish']) : false);
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
        if (! file_exists($project->sourceDirectory)) {
            $output->writeln('[!] The project source path could not be opened at ['.$project->sourceDirectory.']');

            return false;
        }

        /** @var ContentTypeFactory $contentTypes */
        $contentTypes = $project->get('content_types');

        foreach ($contentTypes->all() as $contentType) {
            $this->excluded->addUnderscoreException($contentType->getPath());
        }
        unset($contentType);

        /** @var ContentRendererFactory $contentRenderers */
        $contentRenderers = $project->get('content_renderers');

        $finder = new Finder();
        $finder->files()
            ->followLinks()
            ->in($project->sourceDirectory)
            ->ignoreDotFiles(true);

        $this->excluded->excludeFromFinder($finder);

        foreach ($finder->files() as $file) {
            $file = new File($file, [
                'pretty_permalink' => $this->prettyPermalink,
                'language' => config('language', 'en')
            ]);
            $renderer = $contentRenderers->get($file->getFileInfo()->getExtension());

            if ($renderer->supportsFrontMatter()) {
                $frontMatter = new FrontMatter($file->getFileContent());
                $file->setData($frontMatter->getData());
                $file->setContent($frontMatter->getContent());
            }

            // Publish Drafts / Scheduled Posts
            if ($this->publishDrafts === false) {
                // If file is a draft and cant auto publish then it remains a draft
                if (
                    boolval($file->getData('draft', false)) === true &&
                    $this->canAutoPublish($file) === false
                ) {
                    continue;
                }

                // If file is not a draft, but the date is in the future then it is scheduled
                if ($file->getData('date', new \DateTime()) > $this->now) {
                    continue;
                }
            }

            if (! $contentType = $contentTypes->find($file->getFileInfo()->getRelativePath())) {
                $contentType = $contentTypes->get('*');
            } else {
                $contentType = $contentTypes->get($contentType);
            }

            // Identify if $file belongs to default renderer and therefore should be copied (for issue #255)
            if ($renderer->getName() === 'DefaultRenderer') {
                $renderer->mutateFile($file);
            }

            $contentType->addFile($file);
            $project->addFile($file);

            $output->writeln('[+] File ['.$file->getFileInfo()->getRelativePathname().'] bucketed into content type ['.$contentType->getName().']');
        }

        $output->writeln('[+] Discovered ['.$project['files']->count().'] project files');

        return true;
    }

    /**
     * If the file is a draft, but auto publish is enabled and the files date is in the past then it should be published.
     * @param File $file
     * @version 1.0.9
     * @return bool
     */
    private function canAutoPublish(File $file)
    {
        if ($this->autoPublish === false) {
            return false;
        }

        if ($file->getData('date', new \DateTime()) <= $this->now) {
            return true;
        }

        return false;
    }
}
