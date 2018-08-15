<?php

namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFileGeneratorInterface;

/**
 * Class TaxonomyArchiveGenerator
 * @deprecated
 */
class TaxonomyArchiveGenerator extends FileGenerator implements ProjectFileGeneratorInterface
{
    /**
     * Look up the files content types via the use statement and then look up the taxonomy that has been injected
     * into this files data array by the ContentType parser and create a clone of the File once each for every
     * Taxonomy name with the Files passed to them.
     *
     * @param Project $project
     * @return array|\Tapestry\Entities\ProjectFile
     * @throws \Exception
     */
    public function generate(Project $project)
    {
        $generated = [];

        if (! $uses = $this->file->getData('use')) {
            return $this->file;
        }

        foreach ($uses as $use) {
            if (! $data = $this->file->getData($use.'_items')) {
                continue;
            }

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
                array_push($generated, $newFile);
                unset($newFile);
            }
        }

        return $generated;
    }
}
