<?php

namespace Tapestry\Entities;

use DateTime;
use Symfony\Component\Finder\SplFileInfo;

class File implements ProjectFileInterface
{
    /**
     * Unique Identifier for this File.
     *
     * @var null|string
     */
    private $uid = null;

    /**
     * Unix timestamp of when the content of this file was last modified.
     *
     * @var int
     */
    private $lastModified;

    /**
     * @var SplFileInfo
     */
    private $fileInfo;

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
    private $path;

    /**
     * File data, usually found via frontmatter.
     *
     * @var array
     */
    private $data = [];

    /**
     * File Content.
     *
     * @var string
     */
    private $content = '';

    /**
     * Has the file content been loaded.
     *
     * @var bool
     */
    private $loaded = false;

    /**
     * Has the file been passed through a Renderer?
     *
     * @var bool
     */
    private $rendered = false;

    /**
     * Is true if the file info has been overwritten.
     *
     * @var bool
     */
    private $overWritten = false;

    /**
     * @var Permalink
     */
    private $permalink;

    /**
     * Should this file be copied from source to destination?
     *
     * @var bool
     */
    private $toCopy = false;

    /**
     * File constructor.
     *
     * @param SplFileInfo $fileInfo
     * @param array $data
     * @throws \Exception
     */
    public function __construct(SplFileInfo $fileInfo, array $data = [])
    {
        $this->fileInfo = $fileInfo;

        $this->setLastModified($this->fileInfo->getMTime());

        $this->filename = pathinfo($fileInfo->getBasename(), PATHINFO_FILENAME);
        $this->ext = pathinfo($fileInfo->getBasename(), PATHINFO_EXTENSION);
        $this->path = $fileInfo->getRelativePath();

        if (strpos($this->filename, '.') !== false) {
            $fe = explode('.', $this->filename);
            $this->filename = array_shift($fe);
            $this->ext = implode('.', $fe).'.'.$this->ext;
        }

        $defaultData = array_merge([
            'date'             => DateTime::createFromFormat('U', $fileInfo->getMTime()),
            'pretty_permalink' => true,
        ], $data);

        $this->permalink = new Permalink();

        preg_match('/^(\d{4}-\d{2}-\d{2})-(.*)/', $this->fileInfo->getBasename('.'.$this->fileInfo->getExtension()),
            $matches);
        if (count($matches) === 3) {
            $defaultData['date'] = new DateTime($matches[1]);
            $defaultData['draft'] = false;
            $defaultData['slug'] = $matches[2];
            $defaultData['title'] = ucfirst(str_replace('-', ' ', $defaultData['slug']));
        }
        $this->setData($defaultData);
    }

    /**
     * Get identifier for this file, the relative pathname is unique to each file so that should be good enough.
     *
     * @return string
     */
    public function getUid()
    {
        if (is_null($this->uid)) {
            $pathName = (! empty($this->getFileInfo()->getRelativePathname())) ? $this->getFileInfo()->getRelativePathname() : $this->getFileInfo()->getPathname();
            $this->uid = $this->cleanUid($pathName);
        }

        return $this->uid;
    }

    /**
     * Set the files uid.
     *
     * @param string$uid
     */
    public function setUid($uid)
    {
        $this->uid = $this->cleanUid($uid);
    }

    private function cleanUid($uid)
    {
        $uid = str_replace('.', '_', $uid);
        $uid = str_replace(['/', '\\'], '_', $uid);

        return $uid;
    }

    /**
     * Returns the SplFileInfo class that the Symfony Finder created.
     *
     * @return SplFileInfo
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * @param SplFileInfo $fileInfo
     */
    public function setFileInfo(SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
        $this->filename = pathinfo($fileInfo->getBasename(), PATHINFO_FILENAME);
        $this->ext = pathinfo($fileInfo->getBasename(), PATHINFO_EXTENSION);
        $this->path = $fileInfo->getRelativePath();
        $this->overWritten = true;

        if ($this->getLastModified() < $fileInfo->getMTime()) {
            $this->setLastModified($fileInfo->getMTime());
        }
    }

    /**
     * Returns the file content, this will be excluding any frontmatter.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getContent()
    {
        if (! $this->isLoaded()) {
            throw new \Exception('The file ['.$this->fileInfo->getRelativePathname().'] has not been loaded.');
        }

        return $this->content;
    }

    /**
     * Set the files content, this should be excluding any frontmatter.
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->loaded = true;
    }

    public function setPermalink(Permalink $permalink)
    {
        $this->permalink = $permalink;
    }

    /**
     * Pretty Permalinks are disabled on all files that have their
     * permalink structure configured via front matter.
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function getCompiledPermalink()
    {
        $pretty = $this->getData('pretty_permalink', true);
        if ($this->hasData('permalink')) {
            $pretty = false;
        }

        return $this->permalink->getCompiled($this, $pretty);
    }

    public function getPermalink()
    {
        return $this->permalink;
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
     * Set by the Compile class when it passes this File through a Renderer.
     *
     * @param $value
     */
    public function setRendered($value)
    {
        $this->rendered = boolval($value);
    }

    /**
     * Has this File been passed through a Renderer?
     *
     * @return bool
     */
    public function isRendered()
    {
        return $this->rendered;
    }

    /**
     * Set this files data (via frontmatter or other source).
     *
     * @param array $data
     * @throws \Exception
     */
    public function setData(array $data)
    {
        if (isset($data['date']) && ! ($data['date'] instanceof DateTime)) {
            $date = new DateTime();
            if (! $unix = strtotime($data['date'])) {
                if (! $unix = strtotime('@'.$data['date'])) {
                    throw new \Exception('The date ['.$data['date'].'] is in a format not supported by Tapestry.');
                }
            }
            $data['date'] = $date->createFromFormat('U', $unix);
        }

        $this->data = array_merge($this->data, $data);

        if ($permalink = $this->getData('permalink')) {
            $this->permalink = new Permalink($permalink);
        }
    }

    /**
     * Return this files data (set via frontmatter if any is found).
     *
     * @param null $key
     * @param null $default
     *
     * @return array|mixed|null
     */
    public function getData($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->data;
        }
        if (! $this->hasData($key)) {
            return $default;
        }

        return $this->data[$key];
    }

    /**
     * Return true if this file has data set for $key.
     * @param $key
     * @return bool
     */
    public function hasData($key)
    {
        return isset($this->data[$key]);
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

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * @param string $ext
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return bool
     */
    public function isToCopy()
    {
        return $this->toCopy;
    }

    /**
     * @param bool $toCopy
     */
    public function setToCopy($toCopy)
    {
        $this->toCopy = $toCopy;
    }

    /**
     * @return int
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param int $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }
}
