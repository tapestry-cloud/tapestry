<?php namespace Tapestry\Entities\Filesystem;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\File;

class FileWriter
{
    /**
     * @var File
     */
    private $file;
    private $destinationPath;

    public function __construct(File $file, $destinationPath)
    {
        $this->file = $file;
        $this->destinationPath = $destinationPath;
    }

    public function __invoke(Filesystem $filesystem, OutputInterface $output)
    {
        $outputPath = $this->file->getCompiledPermalink();
        $output->writeln('[+] Writing File ['. $this->file->getUid() .'] to path ['. $outputPath .']');
        $filesystem->dumpFile($this->destinationPath . DIRECTORY_SEPARATOR . $outputPath, $this->file->getContent());
    }
}