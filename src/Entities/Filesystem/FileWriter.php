<?php

namespace Tapestry\Entities\Filesystem;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class FileWriter extends FileAction implements FilesystemInterface
{
    /**
     * @param Filesystem      $filesystem
     * @param OutputInterface $output
     *
     * @return void
     */
    public function __invoke(Filesystem $filesystem, OutputInterface $output)
    {
        $outputPath = $this->file->getCompiledPermalink();
        $output->writeln('[+] Writing File ['.$this->file->getUid().'] to path ['.$outputPath.']');
        $filesystem->dumpFile($this->destinationPath.DIRECTORY_SEPARATOR.$outputPath, $this->file->getContent());
    }
}
