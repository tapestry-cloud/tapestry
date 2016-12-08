<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Entities\File;
use Tapestry\Entities\Cache;
use Tapestry\Entities\Project;
use Tapestry\Entities\ViewFile;
use Tapestry\Entities\ContentType;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\Filesystem\FileCopier;
use Tapestry\Entities\Filesystem\FileWriter;
use Tapestry\Entities\Filesystem\FileIgnored;
use Tapestry\Entities\Generators\FileGenerator;
use Tapestry\Entities\Collections\FlatCollection;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;
use Tapestry\Modules\Renderers\ContentRendererFactory;

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
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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
        /** @var ContentTypeFactory $contentTypes */
        $contentTypes = $project->get('content_types');

        /** @var ContentRendererFactory $contentRenderers */
        $contentRenderers = $project->get('content_renderers');

        /** @var Cache $cache */
        $cache = $project->get('cache');

        //
        // Iterate over the file list of all content types and add the files they contain to the local compiled file list
        // also at this point run any generators that the file may be linked to.
        //
        /** @var ContentType $contentType */
        foreach ($contentTypes->all() as $contentType) {
            $output->writeln('[+] Compiling content within ['.$contentType->getName().']');
            $project->set('compiled', $this->files);

            // Foreach ContentType look up their Files and run the particular Renderer on their $content before updating
            // the Project File.
            foreach (array_keys($contentType->getFileList()) as $fileKey) {
                /** @var File $file */
                if (! $file = $project->get('files.'.$fileKey)) {
                    continue;
                }

                // Pre-compile via use of File Generators
                if ($file instanceof FileGenerator) {
                    $this->add($file->generate($project));
                } else {
                    $this->add($file);
                }
                $project->set('compiled', $this->files);
            }
        }

        //
        // Where a file has a use statement, we now need to collect the associated use data and inject it
        //
        /** @var File $file */
        foreach ($project['compiled'] as $file) {
            if (! $uses = $file->getData('use')) {
                continue;
            }
            foreach ($uses as $use) {
                if (! $items = $file->getData($use.'_items')) {
                    continue;
                }

                array_walk_recursive($items, function (&$file, $fileKey) use ($project) {
                    /** @var File $compiledFile */
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

        if (! $this->allFilesGenerated()) {
            foreach ($this->files as &$file) {
                if ($uses = $file->getData('generator')) {
                    if (count($uses) > 0) {
                        $output->writeln('[!] File ['.$file->getUid().'] has not completed generating');
                    }
                }
            }
            unset($file);

            exit(1);
        }

        //
        // Execute Renderers
        //
        while (! $this->allFilesRendered()) {
            foreach ($this->files as &$file) {
                if ($file->isRendered()) {
                    continue;
                }
                $fileRenderer = $contentRenderers->get($file->getExt());
                $file->setContent($fileRenderer->render($file));
                $file->setExt($fileRenderer->getDestinationExtension($file->getExt()));
                $file->setRendered(true);
                $fileRenderer->mutateFile($file);
            }
            unset($file);
        }

        //
        // Mutate into FileCopy or FileWrite entities
        //
        foreach ($this->files as &$file) {
            if ($cachedCTime = $cache->getItem($file->getUid())) {
                if ($file->getLastModified() == $cachedCTime) {
                    $file = new FileIgnored(clone $file, $project->destinationDirectory);
                    continue;
                }
            }

            if ($file->isToCopy()) {
                $file = new FileCopier(clone $file, $project->destinationDirectory);
            } else {
                $file = new FileWriter(clone $file, $project->destinationDirectory);
            }
        }
        unset($file);

        $project->set('compiled', new FlatCollection($this->files));

        return true;
    }

    /**
     * @param File|File[] $files
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
    private function allFilesRendered()
    {
        foreach ($this->files as $file) {
            if (! $file->isRendered()) {
                return false;
            }
        }

        return true;
    }

    private function allFilesGenerated()
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
