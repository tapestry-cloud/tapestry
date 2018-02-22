<?php

namespace Tapestry\Steps;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Cache;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFile;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;
use Tapestry\Modules\Renderers\ContentRendererFactory;
use Tapestry\Step;
use Tapestry\Tapestry;

class LoadSourceFileTree implements Step
{

    /**
     * @var Tapestry
     */
    private $tapestry;

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
     * Paths that should be ignored.
     *
     * @var array
     */
    private $ignoredPaths = [];

    /**
     * LoadSourceFiles constructor.
     *
     * @param Tapestry $tapestry
     * @param Configuration $configuration
     */
    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->tapestry = $tapestry;
        $this->now = new \DateTime();
        $this->ignoredPaths = array_merge($configuration->get('ignore'), ['_views', '_templates']);
        $this->prettyPermalink = boolval($configuration->get('pretty_permalink', true));
        $this->publishDrafts = boolval($configuration->get('publish_drafts', false));
        $this->autoPublish = (isset($tapestry['cmd_options']['auto-publish']) ? boolval($tapestry['cmd_options']['auto-publish']) : false);
    }

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Exception
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        if (!file_exists($project->sourceDirectory)) {
            $output->writeln('[!] The project source path could not be opened at [' . $project->sourceDirectory . ']');

            return false;
        }

        /** @var Cache $cache */
        $cache = $project->get('cache');

        // Table containing all files found and their last update time.
        if (!$hashTable = $cache->getItem('fileHashTable')) {
            $hashTable = [];
        }

        /** @var ContentTypeFactory $contentTypes */
        $contentTypes = $project->get('content_types');

        foreach ($contentTypes->all() as $contentType) {
            $path = $contentType->getPath();
            if ($path !== '*' && ! isset($this->ignoredPaths[$contentType->getPath()])) {
                $this->ignoredPaths[] = $contentType->getPath();
            }
        }
        unset($contentType);

        /** @var ContentRendererFactory $contentRenderers */
        $contentRenderers = $project->get('content_renderers');

        $finder = new Finder();
        $finder->files()
            ->followLinks()
            ->in($project->sourceDirectory)
            ->ignoreDotFiles(true);

        foreach ($finder->files() as $file) {
            $file = new ProjectFile($file, ['pretty_permalink' => $this->prettyPermalink]);
            if (isset($hashTable[$file->getUid()]) && $hashTable[$file->getUid()] == $file->getMTime()) {
                $file->isBlocked();
            } else {
                $hashTable[$file->getUid()] = $file->getMTime();
            }

            if ($this->shouldIgnore($file)) {
                $file->setIgnored();
            } else {

                $renderer = $contentRenderers->get($file->getFileInfo()->getExtension());

                if ($renderer->supportsFrontMatter()) {
                    $frontMatter = new FrontMatter($file->getFileContent());
                    $file->setData($frontMatter->getData());
                    $file->loadContent($frontMatter->getContent());
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

                if (!$contentType = $contentTypes->find($file->getRelativePath())) {
                    $contentType = $contentTypes->get('*');
                } else {
                    $contentType = $contentTypes->get($contentType);
                }

                // Identify if $file belongs to default renderer and therefore should be copied (for issue #255)
                if ($renderer->getName() === 'DefaultRenderer') {
                    $renderer->mutateFile($file);
                }

                $contentType->addFile($file);
            }

            $project->addFile($file);

            if (! $file->isIgnored()) {
                $output->writeln('[+] File [' . $file->getRelativePathname() . '] bucketed into content type [' . $contentType->getName() . ']');
            }
        }

        $cache->setItem('fileHashTable', $hashTable);

        return true;
    }

    /**
     * If the file is a draft, but auto publish is enabled and the files date is in the past then it should be published.
     *
     * @param ProjectFile $file
     * @version 1.0.9
     * @return bool
     */
    private function canAutoPublish(ProjectFile $file)
    {
        if ($this->autoPublish === false) {
            return false;
        }

        if ($file->getData('date', new \DateTime()) <= $this->now) {
            return true;
        }

        return false;
    }

    /**
     * Files found in '_views', '_templates' should have their ignored flag set true.
     * This is so they are available for SyntaxAnalysis but not parsed and output to dist
     * because that would make no sense...
     *
     * @param ProjectFile $file
     * @return bool
     */
    private function shouldIgnore(ProjectFile $file): bool
    {
        $relativePath = $file->getRelativePath();

        foreach ($this->ignoredPaths as $ignoredPath) {
            if (str_contains($relativePath, $ignoredPath)){
                return true;
            }
        }

        // Paths containing underscores are ignored by default.
        foreach (explode('/', str_replace('\\', '/', $relativePath)) as $pathItem) {
            if (substr($pathItem, 0, 1) === '_') {
                return true;
            }
        }

        return false;
    }
}