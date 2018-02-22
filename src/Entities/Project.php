<?php

namespace Tapestry\Entities;

use Tapestry\ArrayContainer;
use Tapestry\Entities\Generators\FileGenerator;
use Tapestry\Entities\Collections\FlatCollection;

class Project extends ArrayContainer
{
    /**
     * @var string
     */
    public $sourceDirectory;

    /**
     * @var string
     */
    public $destinationDirectory;

    /**
     * @var string
     */
    public $currentWorkingDirectory;

    /**
     * @var string
     */
    public $environment;

    /**
     * Project constructor.
     *
     * @param string $cwd
     * @param string $dist
     * @param string $environment
     */
    public function __construct($cwd, $dist, $environment)
    {
        $this->sourceDirectory = $cwd.DIRECTORY_SEPARATOR.'source';
        $this->destinationDirectory = $dist;

        $this->currentWorkingDirectory = $cwd;
        $this->environment = $environment;

        parent::__construct(
            [
                'files' => new FlatCollection(),
            ]
        );
    }

    /**
     * @param ProjectFileInterface|ProjectFile|FileGenerator $file
     */
    public function addFile(ProjectFileInterface $file)
    {
        $this->set('files.'.$file->getUid(), $file);
    }

    /**
     * @param string $key
     *
     * @return ProjectFileInterface|ProjectFile|FileGenerator
     */
    public function getFile($key)
    {
        return $this->get('files.'.$key);
    }

    /**
     * @param ProjectFileInterface|ProjectFile|FileGenerator $file
     */
    public function removeFile(ProjectFileInterface $file)
    {
        $this->remove('files.'.$file->getUid());
    }

    /**
     * @param ProjectFileInterface|ProjectFile|FileGenerator $oldFile
     * @param ProjectFileInterface|ProjectFile|FileGenerator $newFile
     */
    public function replaceFile(ProjectFileInterface $oldFile, ProjectFileInterface $newFile)
    {
        $this->removeFile($oldFile);
        $this->addFile($newFile);
    }

    /**
     * @param string $name
     * @param ProjectFile   $file
     *
     * @return ProjectFileGeneratorInterface
     */
    public function getContentGenerator($name, ProjectFile $file)
    {
        return $this->get('content_generators')->get($name, $file);
    }

    /**
     * @param string $name
     *
     * @return ContentType
     */
    public function getContentType($name)
    {
        return $this->get('content_types.'.$name);
    }
}
