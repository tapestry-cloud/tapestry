<?php namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;

class PaginationGenerator extends FileGenerator
{

    /**
     * @param Project $project
     * @return \Tapestry\Entities\ProjectFileInterface|\Tapestry\Entities\ProjectFileInterface[]
     */
    public function generate(Project $project)
    {

        $newFile = clone($this->file);
        $newFile->setData([
            'generator' => array_filter($this->file->getData('generator'), function($value){
                return $value !== 'PaginationGenerator';
            })
        ]);

        return $newFile;

    }

}