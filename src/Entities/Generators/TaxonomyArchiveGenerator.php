<?php

namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFileGeneratorInterface;

class TaxonomyArchiveGenerator extends FileGenerator implements ProjectFileGeneratorInterface
{
    // Returns either a singular File or an array of File with this generator removed from its list.
    public function generate(Project $project)
    {
        $generated = [];

        // Look up the files content types via the use and then look up the taxonomy that has been injected into this files
        // data array and create a clone of the File once each for every Taxonomy name with the Files passed to them.

        if (! $uses = $this->file->getData('use')) {
            return $this->file;
        }

        foreach ($uses as $use) {
            if (! $data = $this->file->getData($use.'_items')) {
                continue;
            }

            // This is a work around to get my photogabble theme up and running. We should really have a taxonomy object
            // that is passed to the front end with view helpers attached.
            $taxonomyItems = array_keys($data);

            foreach ($data as $taxonomyName => $files) {
                $newFile = clone $this->file;
                $newFile->setData([
                    'generator' => array_filter($this->file->getData('generator'), function ($value) {
                        return $value !== 'TaxonomyArchiveGenerator';
                    }),
                    'taxonomyName' => $taxonomyName,
                    $use.'_items'  => $files,
                    $use => $taxonomyItems,
                ]);

                $newFile->setUid($newFile->getUid().'_'.$taxonomyName);
                $newFile->setFilename($taxonomyName);

                // @todo actually fill in the Files that this uses...

                array_push($generated, $newFile);
                unset($newFile);
            }
        }

        return $generated;
    }
}
