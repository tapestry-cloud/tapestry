<?php

namespace Tapestry\Plates;

use Tapestry\Entities\File;
use League\Plates\Engine as LeagueEngine;

class Engine extends LeagueEngine
{
    /**
     * Create a new template.
     *
     * @param string $name
     *
     * @return Template
     */
    public function make($name)
    {
        return new Template($this, $name);
    }

    /**
     * Create a new template and render it.
     *
     * @param File   $file
     * @param string $tmpDirectory
     *
     * @return string
     */
    public function renderFile(File $file, $tmpDirectory)
    {
        return $this->make(
            $file->getFileInfo()->getRelativePath().
            DIRECTORY_SEPARATOR.
            pathinfo($file->getFileInfo()->getFilename(), PATHINFO_FILENAME)
        )->renderFile($file, $tmpDirectory);
    }
}
