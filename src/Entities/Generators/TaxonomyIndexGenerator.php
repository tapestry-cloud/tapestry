<?php namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;

class TaxonomyIndexGenerator extends FileGenerator
{

    public function generate(Project $project)
    {
        return $this->file;
    }

}