<?php namespace Tapestry\Entities;

use Symfony\Component\Finder\SplFileInfo;

class File
{
    /**
     * @var SplFileInfo
     */
    private $fileInfo;

    /**
     * File data, usually found via frontmatter
     * @var array
     */
    private $data = [];

    /**
     * File Content
     * @var string
     */
    private $content = '';

    /**
     * Has the file content been loaded
     * @var bool
     */
    private $loaded = false;

    /**
     * File constructor.
     * @param SplFileInfo $fileInfo
     */
    public function __construct(SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
        $defaultData = [];

        preg_match('/^(\d{4}-\d{2}-\d{2})-(.*)/', $this->fileInfo->getFilename(), $matches);
        if (count($matches) === 3) {
            $defaultData['date'] = new \DateTime($matches[1]);
            $defaultData['slug'] = $matches[2];
            $defaultData['title'] = ucfirst(str_replace('-', ' ', $defaultData['slug']));
        }
        $this->setData($defaultData);
    }

    /**
     * Get identifier for this file, the relative pathname is unique to each file so that should be good enough
     * @return string
     */
    public function getUid()
    {
        return str_replace('.', '_', $this->getFileInfo()->getRelativePathname());
    }

    /**
     * Returns the SplFileInfo class that the Symfony Finder created
     *
     * @return SplFileInfo
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * Returns the file content, this will be excluding any frontmatter
     * @return string
     * @throws \Exception
     */
    public function getContent()
    {
        if (!$this->isLoaded()) {
            throw new \Exception('The file [' . $this->fileInfo->getRelativePathname() . '] has not been loaded.');
        }
        return $this->content;
    }

    /**
     * Set the files content, this should be excluding any frontmatter
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->loaded = true;
    }

    /**
     * A file can be considered loaded once its content property has been set, that way you know any frontmatter has
     * also been injected into the File objects data property.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Set this files data (via frontmatter or other source)
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Return this files data (set via frontmatter if any is found)
     *
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function getData($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->data;
        }
        if (!isset($this->data[$key])) {
            return $default;
        }
        return $this->data[$key];
    }

    /**
     * Get the content of the file that this object relates to.
     *
     * @return string
     */
    public function getFileContent()
    {
        return file_get_contents($this->fileInfo->getPathname());
    }
}