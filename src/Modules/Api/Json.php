<?php

namespace Tapestry\Modules\Api;

use Tapestry\Entities\ProjectFile;
use Tapestry\Step;
use Tapestry\Entities\Project;
use Tapestry\Entities\Taxonomy;
use Tapestry\Entities\ContentType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;

class Json implements Step
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Json constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Process the Project at current.
     *
     * Note: This Step should ALWAYS be loaded after the LoadSourceFiles step. This is because it exports $project['files']
     * to JSON as created by the LoadSourceFiles step. That array is mutated by subsequent steps (is that a bug?) and so
     * is not the expected input for the JSON export.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Exception
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        if ($project->get('cmd_options.json') !== true) {
            return true;
        }

        $output->writeln('[+] Exporting JSON Blob');

        $json = [
            'exported' => date('c'),
            'files' => [],
            'content_types' => [],
        ];

        /**
         * @var string
         * @var ProjectFile $file
         */
        foreach ($project->get('files') as $id => $file) {
            $json['files'][$id] = [
                'filename' => $file->getFilename(),
                'ext' => $file->getExt(),
                'data' => $file->getData(),
                'content' => $file->getFileContent(),
                'modified' => $file->getMTime(),
                'path' => $file->getPath(),
            ];
        }

        /** @var ContentTypeFactory $contentTypes */
        $contentTypes = $project->get('content_types');

        /**
         * @var string
         * @var ContentType $contentType
         */
        foreach ($contentTypes->all() as $id => $contentType) {
            $json['content_types'][$contentType->getName()] = [
                'name' => $contentType->getName(),
                'path' => $contentType->getPath(),
                'permalink' => $contentType->getPermalink(),
                'taxonomies' => [],
            ];

            /** @var Taxonomy $taxonomy */
            foreach ($contentType->getTaxonomies() as $taxonomy) {
                $json['content_types'][$contentType->getName()]['taxonomies'][] = $taxonomy->getName();
            }
        }

        $this->filesystem->dumpFile($project->currentWorkingDirectory.DIRECTORY_SEPARATOR.'db.json', json_encode($json));

        return $this->filesystem->exists($project->currentWorkingDirectory.DIRECTORY_SEPARATOR.'db.json');
    }
}
