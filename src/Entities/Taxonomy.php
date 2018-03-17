<?php

namespace Tapestry\Entities;

use Tapestry\Entities\Collections\Collection;
use Tapestry\Modules\Source\SourceInterface;

class Taxonomy
{
    /**
     * The name of this Taxonomy.
     *
     * @var string
     */
    private $name;

    /**
     * Collection of Entities\File that this Taxonomy has collected.
     *
     * @var Collection
     */
    private $items;

    /**
     * ContentType constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = str_slug($name);
        $this->items = new Collection();
    }

    /**
     * The taxonomy name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param SourceInterface $file
     * @param $classification
     */
    public function addFile(SourceInterface $file, $classification)
    {
        $classification = str_slug($classification);
        if (! $this->items->has($classification)) {
            $this->items->set($classification, []);
        }

        $this->items->set($classification.'.'.$file->getUid(), $file->getData('date')->getTimestamp());
    }

    /**
     * Returns an ordered list of the file uid's that have been bucketed into this taxonomy. The list is ordered by
     * the files date.
     *
     * @param string $order
     * @return array
     */
    public function getFileList($order = 'desc')
    {
        $order = strtolower(trim($order));
        // Order Files by date newer to older
        $this->items->sortMultiDimension(function ($a, $b) use ($order) {
            if ($a == $b) {
                return 0;
            }
            if ($order === 'asc') {
                return ($a < $b) ? -1 : 1;
            }

            return ($a > $b) ? -1 : 1;
        });

        return $this->items->all();
    }
}
