<?php

namespace Tapestry\Entities\Filesystem;

use Tapestry\Entities\ProjectFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

interface FilesystemInterface
{
    /**
     * FilesystemInterface constructor.
     *
     * @param ProjectFile   $file
     * @param string $destinationPath
     */
    public function __construct(ProjectFile $file, $destinationPath);

    /**
     * @return ProjectFile
     */
    public function getFile();

    /**
     * @param Filesystem      $filesystem
     * @param OutputInterface $output
     *
     * @return void
     */
    public function __invoke(Filesystem $filesystem, OutputInterface $output);
}
