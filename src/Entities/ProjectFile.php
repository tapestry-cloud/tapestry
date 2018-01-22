<?php

namespace Tapestry\Entities;

use Symfony\Component\Finder\SplFileInfo;

class ProjectFile extends SplFileInfo
{
    /**
     * File meta data, usually from front matter or site config.
     * @var array
     */
    private $meta = [];

    public function __construct(SplFileInfo $file, $data = [])
    {
        parent::__construct($file->getFilename(), $file->getRelativePath(), $file->getRelativePathname());
        $this->boot($data);
    }

    public function boot(array $data = [])
    {

    }

}