<?php

namespace Tapestry\Modules\Source;

use Tapestry\Entities\Permalink;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class SplFileSource.
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
        $this->meta = [];
        $this->permalink = new Permalink();

        $this->setDataFromArray($data);
        $this->setUid((! empty($this->getRelativePathname())) ? $this->getRelativePathname() : $file->getPathname());

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
     * Gets the filename.
     *
     * @param bool $overloaded
     * @return string
     */
    public function getFilename(bool $overloaded = true): string
    {
        if ($overloaded === true && isset($this->overloaded['filename'])) {
            return $this->overloaded['filename'];
        }

        return $this->splFileInfo->getFilename();
    }

    /**
     * Gets the file extension.
     *
     * @param bool $overloaded
     * @return string
     */
    public function getExtension(bool $overloaded = true): string
    {
        if ($overloaded === true && isset($this->overloaded['ext'])) {
            return $this->overloaded['ext'];
        }

        return $this->splFileInfo->getExtension();
    }

    /**
     * Returns the relative path.
     * This path does not contain the file name.
     *
     * @param bool $overloaded
     * @return string the relative path
     */
    public function getRelativePath(bool $overloaded = true): string
    {
        if ($overloaded === true && isset($this->overloaded['relativePath'])) {
            return $this->overloaded['relativePath'];
        }

        return $this->splFileInfo->getRelativePath();
    }

    /**
     * Returns the relative path name.
     * This path contains the file name.
     *
     * @param bool $overloaded
     * @return string
     */
    public function getRelativePathname(bool $overloaded = true): string
    {
        if ($overloaded === true && isset($this->overloaded['relativePathname'])) {
            return $this->overloaded['relativePathname'];
        }

        return $this->splFileInfo->getRelativePathname();
    }
}
