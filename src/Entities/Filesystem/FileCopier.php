<?php

namespace Tapestry\Entities\Filesystem;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class FileCopier extends FileAction implements FilesystemInterface
{
    /**
     * @param Filesystem      $filesystem
     * @param OutputInterface $output
     *
     * @return void
     */
    public function __invoke(Filesystem $filesystem, OutputInterface $output)
    {
        $outputPath = $this->file->getCompiledPermalink(false);
        $output->writeln('[+] Copying File ['.$this->file->getUid().'] to path ['.$outputPath.']');
        $filesystem->copy($this->file->getFileInfo()->getPathname(), $this->destinationPath.DIRECTORY_SEPARATOR.$outputPath);
    }
}
