<?php

namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;

class TaxonomyIndexGenerator extends FileGenerator
{
    public function generate(Project $project)
    {
        if (! $this->file->hasData('use')) {
            $this->file->setData([
                'generator' => array_filter($this->file->getData('generator'), function ($value) {
                    return $value !== 'TaxonomyIndexGenerator';
                }),
            ]);
            return $this->file;
        }

        $newFile = clone $this->file;
        $newFile->setData([
            'generator' => array_filter($this->file->getData('generator'), function ($value) {
                return $value !== 'TaxonomyIndexGenerator';
            }),
        ]);

        return $newFile;
    }
}
