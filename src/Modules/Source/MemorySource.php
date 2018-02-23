<?php

namespace Tapestry\Modules\Source;

use Tapestry\Entities\Permalink;

/**
 * Class SplFileSource
 * @package Tapestry\Modules\Source
 */
class MemorySource extends AbstractSource implements SourceInterface
{

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $ext;

    /**
     * @var string
     */
    private $relativePath;

    /**
     * @var string
     */
    private $relativePathname;
    /**
     * @var string
     */
    private $rawContent;

    /**
     * MemorySource constructor.
     *
     * @param string $uid
     * @param string $rawContent
     * @param string $filename
     * @param string $ext
     * @param string $relativePath
     * @param string $relativePathname
     * @param array $data
     * @throws \Exception
     */
    public function __construct(
        string $uid,
        string $rawContent,
        string $filename,
        string $ext,
        string $relativePath,
        string $relativePathname,
        array $data = []
    ){
        $this->meta = [];
        $this->permalink = new Permalink();
        $this->setDataFromArray($data);
        $this->setUid($uid);
        $this->filename = $filename;
        $this->ext = $ext;
        $this->relativePath = $relativePath;
        $this->relativePathname = $relativePathname;
        $this->rawContent = $rawContent;
    }

    /**
     * Get the content of the file that this object relates to.
     *
     * @throws \Exception
     * @return string
     */
    public function getRawContent(): string
    {
        return $this->rawContent;
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

        return $this->filename;
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

        return $this->ext;
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
        if ($overloaded === true && isset($this->overloaded['relativePath'])){
            return $this->overloaded['relativePath'];
        }

        return $this->relativePath;
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
        if ($overloaded === true && isset($this->overloaded['relativePathname'])){
            return $this->overloaded['relativePathname'];
        }

        return $this->relativePathname;
    }
}