<?php

namespace Tapestry\Modules\Source;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Class SplFileSource
 * @package Tapestry\Modules\Source
 *
 *
 *
 */
class SplFileSource extends AbstractSource implements SourceInterface
{

    /**
     * @var SplFileInfo
     */
    private $splFileInfo;

    /**
     * SplFileSource constructor.
     *
     * @param SplFileInfo $file
     * @param array $data
     * @param bool $autoBoot
     * @throws \Exception
     */
    public function __construct(SplFileInfo $file, $data = [], $autoBoot = true)
    {
        $this->splFileInfo = $file;
        // if ($autoBoot === true) {
        //     $this->boot($data);
        // }
    }

    /**
     * Get the content of the file that this object relates to.
     *
     * @throws \Exception
     * @return string
     */
    public function getRawContent(): string
    {
        return $this->splFileInfo->getContents();
    }

    /**
     * Set the files content, this should be excluding any frontmatter.
     *
     * @param string $content
     */
    public function setRenderedContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * Returns the file content, this will be excluding any frontmatter.
     *
     * @throws \Exception
     * @return string
     */
    public function getRenderedContent(): string
    {
        if (! $this->hasContent()) {
            throw new \Exception('The file ['.$this->getRelativePathname().'] has not been loaded.');
        }

        return $this->content;
    }

    public function setOverloaded(string $key, $value)
    {
        // TODO: Implement setOverloaded() method.
    }

    public function getFilename(bool $overloaded = true): string
    {
        // TODO: Implement getFilename() method.
    }

    public function getExtension(bool $overloaded = true): string
    {
        // TODO: Implement getExtension() method.
    }

    public function isRendered(): bool
    {
        // TODO: Implement isRendered() method.
    }

    public function setRendered(bool $value = true)
    {
        // TODO: Implement setRendered() method.
    }

    public function isToCopy(): bool
    {
        // TODO: Implement isToCopy() method.
    }

    public function setToCopy(bool $value = true)
    {
        // TODO: Implement setToCopy() method.
    }

    public function isIgnored(): bool
    {
        // TODO: Implement isIgnored() method.
    }

    public function setIgnored(bool $value = true)
    {
        // TODO: Implement setIgnored() method.
    }
}