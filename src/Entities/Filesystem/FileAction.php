<?php

namespace Tapestry\Entities\Filesystem;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\File;

abstract class FileAction implements FilesystemInterface
{
    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $destinationPath;

    /**
     * FilesystemInterface constructor.
     *
     * @param File   $file
     * @param string $destinationPath
     */
    public function __construct(File $file, $destinationPath)
    {
        $this->file = $file;
        $this->destinationPath = $destinationPath;
    }

    /**
     * @return File
     */
    public function getFile()
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
