<?php

namespace Tapestry\Steps;

use Tapestry\Step;
use League\Plates\Engine;
use Tapestry\Entities\Cache;
use Tapestry\Entities\Project;
use Tapestry\Entities\ViewFile;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\ProjectFile;
use Tapestry\Entities\ProjectFileInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\Generators\FileGenerator;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Modules\Renderers\ContentRendererFactory;
use Tapestry\Modules\ContentTypes\ContentTypeCollection;

class SyntaxAnalysis implements Step
{
    /**
     * @var Engine
     */
    private $plates;

    /**
     * @var array|ProjectFile[]
     */
    private $files = [];

    /**
     * @var array
     */
    private $permalinkTable = [];

    /**
     * SyntaxAnalysis constructor.
     * @param Engine $plates
     */
    public function __construct(Engine $plates)
    {
        $this->plates = $plates;
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
        //
        // Identify all source files and build the initial symbol table
        //

        /** @var ContentTypeCollection $contentTypes */
        $contentTypes = $project->get('content_types');

        /** @var ContentRendererFactory $contentRenderers */
        $contentRenderers = $project->get('content_renderers');

        /** @var Cache $cache */
        $cache = $project->get('cache');

        //
        // For V4 of plates, this Analysis should build a .tmp folder containing the source files converted into
        // .phtml files. Once complete each phtml file in the .tmp folder can be output to dist as html.
        //
        // We are essentially compiling from md, html, etc into phtml files and then passing those through Plates.
        //
        // The syntax tree will allow us to see which source files have changed and which phtml intermediate files
        // those changes affect, so that for each build execution we only have to pass those affected intermediate
        // files through Plates.
        //
        // @todo analyse files with changes and only write intermediate files that have changes
        //

        $this->iterateProjectContentTypes($contentTypes, $project, $output);
        $this->collectProjectFilesUseData($project);

        if (! $this->checkForFileGeneratorError($output)) {
            return false;
        }

        if (! $this->checkForPermalinkClashes($project, $output)) {
            return false;
        }

        $this->writeIntermediateFiles($project, $contentRenderers);

        return true;
    }

    /**
     * @param Project $project
     * @param ContentRendererFactory $contentRenderers
     * @throws \Exception
     */
    private function writeIntermediateFiles(Project $project, ContentRendererFactory $contentRenderers)
    {
        $filesystem = new Filesystem();
        $platesCache = $project['plates_cache'] ?? [];
        $intermediatePath = $project->currentWorkingDirectory.DIRECTORY_SEPARATOR.'.compileTmp';

        while (! $this->allFilesRendered()) {
            foreach ($this->files as $file) {
                if ($file->isRendered()) {
                    continue;
                }
                $contentRenderers->renderFile($file);
                if (! $file->isToCopy()) {
                    $permalink = $file->getCompiledPermalink();
                    $platesFilePath = $intermediatePath.$permalink;
                    $filesystem->dumpFile($platesFilePath, $file->getContent());

                    $platesCache[$file->getUid()] = $permalink;

                    if ($layout = $file->getData('layout')) {
                        $filesystem->copy($project->sourceDirectory.DIRECTORY_SEPARATOR.$layout.'.phtml', $intermediatePath.DIRECTORY_SEPARATOR.$layout.'.phtml');
                    }
                }
                $file->setRendered();
            }
        }

        $project['plates_cache'] = $platesCache;
    }

    /**
     * Iterate over the file list of all content types and add the files they contain to the local compiled file list
     * also at this point run any generators that the file may be linked to.
     *
     * @param ContentTypeCollection $contentTypes
     * @param Project $project
     * @param OutputInterface $output
     * @throws \Exception
     */
    private function iterateProjectContentTypes(ContentTypeCollection $contentTypes, Project $project, OutputInterface $output)
    {
        foreach ($contentTypes->all() as $contentType) {
            $output->writeln('[+] Compiling content within ['.$contentType->getName().']');

            // Foreach ContentType look up their Files and run the particular Renderer on their $content before updating
            // the Project File.
            foreach (array_keys($contentType->getFileList()) as $fileKey) {
                /** @var ProjectFile $file */
                if (! $file = $project->get('files.'.$fileKey)) {
                    continue;
                }

                if ($file->isIgnored()) {
                    continue;
                }

                if (! $file->hasData('layout')) {
                    $file->setData('layout', $contentType->getTemplate());
                }

                // Pre-compile via use of File Generators
                if ($file instanceof FileGenerator) { //@todo check if this is ever true
                    $this->add($file->generate($project));
                } else {
                    $this->add($file);
                }
                $project->set('compiled', $this->files);
            }
        }

        $project->set('compiled', $this->files);
    }

    /**
     * @param Project $project
     * @param OutputInterface $output
     * @return bool
     * @throws \Exception
     */
    private function checkForPermalinkClashes(Project $project, OutputInterface $output) : bool
    {
        /** @var ProjectFile $file */
        foreach ($project['compiled'] as $file) {
            if (isset($this->permalinkTable[sha1($file->getCompiledPermalink())])) {
                $output->writeln('<error>[!]</error> The permalink ['.$file->getCompiledPermalink().'] is already in use!');

                return false;
            }
            $this->permalinkTable[sha1($file->getCompiledPermalink())] = [
                'uid' => $file->getUid(),
                'permalink' => $file->getCompiledPermalink(),
            ];
        }

        return true;
    }

    /**
     * @param OutputInterface $output
     * @return bool
     */
    private function checkForFileGeneratorError(OutputInterface $output) : bool
    {
        if (! $this->allFilesGenerated()) {
            foreach ($this->files as &$file) {
                if ($uses = $file->getData('generator')) {
                    if (count($uses) > 0) {
                        $output->writeln('[!] File ['.$file->getUid().'] has not completed generating');
                    }
                }
            }
            unset($file);

            return false;
        }

        return true;
    }

    /**
     * Where a file has a use statement, we now need to collect the associated use data and inject it.
     *
     * @param Project $project
     * @throws \Exception
     */
    private function collectProjectFilesUseData(Project $project)
    {
        /** @var ProjectFile $file */
        foreach ($project['compiled'] as $file) {
            if (! $uses = $file->getData('use')) {
                continue;
            }
            foreach ($uses as $use) {
                if (! $items = $file->getData($use.'_items')) {
                    continue;
                }

                array_walk_recursive($items, function (&$file, $fileKey) use ($project) {
                    /** @var ProjectFile $compiledFile */
                    if (! $compiledFile = $project->get('compiled.'.$fileKey)) {
                        $file = null;
                    } else {
                        $file = new ViewFile($project, $compiledFile->getUid());
                    }
                });

                // Filter out deleted pages, such as drafts
                $items = array_filter($items, function ($value) {
                    return ! is_null($value);
                });

                $file->setData([$use.'_items' => $items]);
            }
        }
    }

    /**
     * @param ProjectFileInterface|ProjectFileInterface[]|ProjectFile|ProjectFile[] $files
     */
    private function add($files)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->files[$file->getUid()] = $file;
            }
        } else {
            $this->files[$files->getUid()] = $files;
        }
    }

    /**
     * Returns true once all the compiled files have been rendered.
     *
     * @return bool
     */
    private function allFilesRendered()  :bool
    {
        foreach ($this->files as $file) {
            if (! $file->isRendered()) {
                return false;
            }
        }

        return true;
    }

    private function allFilesGenerated() : bool
    {
        foreach ($this->files as $file) {
            if ($uses = $file->getData('generator')) {
                if (count($uses) > 0) {
                    return false;
                }
            }
        }

        return true;
    }
}
