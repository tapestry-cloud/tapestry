<?php

namespace Tapestry\Entities\Filesystem;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\File;

interface FilesystemInterface
{
    /**
     * FilesystemInterface constructor.
     *
     * @param File   $file
     * @param string $destinationPath
     */
    public function __construct(File $file, $destinationPath);

    /**
     * @return File
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
