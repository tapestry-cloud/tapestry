<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Entities\Cache;
use Tapestry\Entities\Project;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Filesystem\FilesystemInterface;

class WriteCache implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        /** @var Cache $cache */
        $cache = $project->get('cache');

        /** @var FilesystemInterface $file */
        foreach ($project['compiled']->all() as $file) {
            $f = $file->getFile();
            $cache->setItem($f->getUid(), $f->getLastModified());
        }

        $cache->save();

        return true;
    }
}
