<?php namespace Tapestry\Entities;

use Tapestry\ArrayContainer;
use Tapestry\Entities\Generators\FileGenerator;

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
     * @param $currentWorkingDirectory
     * @param $environment
     */
    public function __construct($currentWorkingDirectory, $environment)
    {
        $this->sourceDirectory = $currentWorkingDirectory . DIRECTORY_SEPARATOR . 'source';
        $this->destinationDirectory = $currentWorkingDirectory . DIRECTORY_SEPARATOR . 'build_' . $environment;

        $this->currentWorkingDirectory = $currentWorkingDirectory;
        $this->environment = $environment;

        parent::__construct(
            [
                'files' => new FlatCollection()
            ]
        );
    }

    /**
     * @param ProjectFileInterface|File|FileGenerator $file
     */
    public function addFile(ProjectFileInterface $file)
    {
        $this['files'][$file->getUid()] = $file;
    }

    /**
     * @param string $key
     * @return ProjectFileInterface|File|FileGenerator
     */
    public function getFile($key)
    {
        return $this['files'][$key];
    }

    /**
     * @param ProjectFileInterface|File|FileGenerator $file
     */
    public function removeFile(ProjectFileInterface $file)
    {
        unset($this['files'][$file->getUid()]);
    }

    /**
     * @param ProjectFileInterface|File|FileGenerator $oldFile
     * @param ProjectFileInterface|File|FileGenerator $newFile
     */
    public function replaceFile(ProjectFileInterface $oldFile, ProjectFileInterface $newFile)
    {
        $this->removeFile($oldFile);
        $this->addFile($newFile);
    }

    /**
     * @param string $name
     * @param File $file
     * @return ProjectFileGeneratorInterface
     */
    public function getContentGenerator($name, File $file)
    {
        return $this->get('content_generators')->get($name, $file);
    }

    /**
     * @param string $name
     * @return ContentType
     */
    public function getContentType($name)
    {
        return $this->get('content_types.'. $name);
    }
}