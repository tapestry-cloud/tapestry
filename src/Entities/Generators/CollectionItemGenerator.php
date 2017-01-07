<?php

namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;
use Tapestry\Entities\Pagination;

class CollectionItemGenerator extends FileGenerator
{
    public function generate(Project $project)
    {
        $newFile = clone $this->file;
        $newFile->setData([
            'generator' => array_filter($this->file->getData('generator'), function ($value) {
                return $value !== 'CollectionItemGenerator';
            }),
        ]);

        // Identify Previous and Next Item within this files collection

        if ($contentType = $this->file->getData('content_type')) {
            $contentType = $project->getContentType($contentType);
        }

        $siblings = array_keys($contentType->getFileList('asc'));
        $position = array_search($this->file->getUid(), $siblings);

        if (($position === (count($siblings) - 1))) {
            // If we are the last page, then Pagination will only have two pages (this and the previous one), also there will
            // be just two files in the items array

            $pagination = new Pagination($project, [], 2, 2);
        } elseif ($position === 0) {
            // If we are the first page, then Pagination will only have two pages (this and the next one), also there will be
            // just two files in the items array

            $pagination = new Pagination($project, [], 2, 1);
        } else {
            // Else this is the middle page of a total of three (previous, this, next)

            $pagination = new Pagination($project, [], 3, 2);
        }

        $pagination->setPreviousNext(
            (isset($siblings[$position - 1]) ? $siblings[$position - 1] : null),
            (isset($siblings[$position + 1]) ? $siblings[$position + 1] : null)
        );

        // @todo check to see if 'item' should be set within the view's scope; it feels weird that this is the only generator doing so
        $newFile->setData(['previous_next' => $pagination, 'item' => $this->file]);

        return $newFile;
    }
}
