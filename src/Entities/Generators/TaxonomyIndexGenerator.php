<?php namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;

class TaxonomyIndexGenerator extends FileGenerator
{

    public function generate(Project $project)
    {
        if (! $uses = $this->file->getData('use')){
            return $this->file; //@todo this should return a stripped version of the generator, otherwise you will get infinite loops?
        }

        $newFile = clone($this->file);
        $newFile->setData([
            'generator' => array_filter($this->file->getData('generator'), function($value){
                return $value !== 'TaxonomyIndexGenerator';
            })
        ]);

        return $newFile;
    }

}