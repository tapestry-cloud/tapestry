<?php

namespace Tapestry\Entities\Filesystem;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\ProjectFile;

abstract class FileAction implements FilesystemInterface
{
    /**
     * @var ProjectFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $destinationPath;

    /**
     * FilesystemInterface constructor.
     *
     * @param ProjectFile   $file
     * @param string $destinationPath
     */
    public function __construct(ProjectFile $file, $destinationPath)
    {
        $this->file = $file;
        $this->destinationPath = $destinationPath;
    }

    /**
     * @return ProjectFile
     */
    public function getFile() : ProjectFile
    {
        return $this->file;
    }

    /**
     * @param Filesystem      $filesystem
     * @param OutputInterface $output
     *
     * @return void
     */
    abstract public function __invoke(Filesystem $filesystem, OutputInterface $output);
}
