<?php namespace Tapestry\Entities;

use Tapestry\ArrayContainer;

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
                'files' => new FlatCollection(),
                'content_types' => new Collection()
            ]
        );
    }

    public function addFile(File $file)
    {
        $this['files'][$file->getFileInfo()->getRelativePathname()] = $file;
    }

    public function removeFile(File $file)
    {
        unset($this['files'][$file->getFileInfo()->getRelativePathname()]);
    }

    public function replaceFile(File $oldFile, File $newFile)
    {
        $this->removeFile($oldFile);
        $this->addFile($newFile);
    }
}