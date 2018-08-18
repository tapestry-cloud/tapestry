<?php

namespace Tapestry\Entities;

use Tapestry\Modules\Source\SourceInterface;

/**
 * Class ViewFile.
 *
 * This is a wrapper around the parsed AbstractSource intended for
 * consumption in the templates files and therefore provides
 * the helper methods available in ViewFileTrait.
 */
class ViewFile
{
    use ViewFileTrait;

    /**
     * @var SourceInterface
     */
    private $source;

    /**
     * ViewFile constructor.
     *
     * @param SourceInterface $source
     */
    public function __construct(SourceInterface $source)
    {
        $this->source = $source;
    }

    /**
     * @return SourceInterface
     */
    public function getSource(): SourceInterface
    {
        return $this->source;
    }
}
