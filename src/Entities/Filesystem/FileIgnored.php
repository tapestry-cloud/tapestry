<?php

namespace Tapestry\Entities\Filesystem;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FileIgnored.
 *
 * Files that are configured to be ignored by Tapestry do not even make it to the compile stage; this class replaces
 * files that have not changed since the last time Tapestry ran - this is so that Tapestry only writes files that need
 * writing and doesn't crash browser sync.
 */
class FileIgnored extends FileAction implements FilesystemInterface
{
    /**
     * @param Filesystem      $filesystem
     * @param OutputInterface $output
     *
     * @return void
     */
    public function __invoke(Filesystem $filesystem, OutputInterface $output)
    {
        $output->writeln('[+] Ignoring File ['.$this->file->getUid().']');
    }
}
