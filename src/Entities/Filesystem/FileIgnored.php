<?php

namespace Tapestry\Entities\Filesystem;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\File;

/**
 * Class FileIgnored.
 *
 * Files that are configured to be ignored by Tapestry do not even make it to the compile stage; this class replaces
 * files that have not changed since the last time Tapestry ran - this is so that Tapestry only writes files that need
 * writing and doesn't crash browser sync.
 */
class FileIgnored implements FilesystemInterface
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

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function __invoke(Filesystem $filesystem, OutputInterface $output)
    {
        $output->writeln('[+] Ignoring File ['.$this->file->getUid().']');
    }
}
