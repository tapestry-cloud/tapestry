<?php

namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Project;
use Tapestry\Entities\ViewFile;

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

        $siblings = array_keys($contentType->getFileList());
        $position = array_search($this->file->getUid(), $siblings);

        $previousNext = new \stdClass();
        $previousNext->isFirst = ($position === 0);
        $previousNext->isLast = ($position === (count($siblings) - 1));
        $previousNext->next = null;
        $previousNext->previous = null;

        if (! $previousNext->isFirst) {
            $previousNext->previous = isset($siblings[$position - 1]) ? new ViewFile($project, $siblings[$position - 1]) : null;
        }

        if (! $previousNext->isLast) {
            $previousNext->next = isset($siblings[$position + 1]) ? new ViewFile($project, $siblings[$position + 1]) : null;
        }

        $newFile->setData(['previous_next' => $previousNext, 'item' => $this->file]);

        return $newFile;
    }
}
