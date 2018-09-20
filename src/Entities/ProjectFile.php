<?php

namespace Tapestry\Entities;

use DateTime;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ProjectFile
 * @deprecated ???
 * @package Tapestry\Entities
 */
class ProjectFile extends SplFileInfo implements ProjectFileInterface
{
    /**
     * File meta data, usually from front matter or site config.
     *
     * @var array
     */
    private $meta = [];

    /**
     * The Permalink object attached to this file.
     *
     * @var Permalink
     */
    private $permalink;

    /**
     * This is the files content.
     *
     * @var bool|string
     */
    private $content = false;

    /**
     * Has the file been passed through a Renderer?
     *
     * @var bool
     */
    private $rendered = false;

    /**
     * Should this file be copied from source to destination?
     * Files marked for copying will not be rendered.
     *
     * @var bool
     */
    private $copy = false;

    /**
     * Overloaded SplFileInfo properties.
     * @var array
     */
    private $overloaded = [];

    /**
     * If a file is blocked, it means it hasn't been changed since the
     * last time it was rendered. This should block outputting the dist
     * file, but not block the file being used.
     *
     * This way we should be able to speed up execution by only
     * generating files that have had their source change since the
     * last execution.
     *
     * @var bool
     */
    private $blocked = false;

    /**
     * Because all files in the source tree are loaded, this ignore flag
     * is set on those that should not be parsed. This allows them to be
     * analysed for dependencies (e.g. a not ignored file depends upon a
     * ignored file).
     *
     * @var bool
     */
    private $ignored = false;

    /**
     * ProjectFile constructor.
     *
     * @param SplFileInfo $file
     * @param array $data
     * @param bool $autoBoot
     * @throws \Exception
     */
    public function __construct(SplFileInfo $file, $data = [], $autoBoot = true)
    {
        parent::__construct($file->getPathname(), $file->getRelativePath(), $file->getRelativePathname());
        if ($autoBoot === true) {
            $this->boot($data);
        }
    }

    /**
     * Boot the file.
     *
     * @param array $data
     * @throws \Exception
     *
     * @return void
     */
    public function boot(array $data = [])
    {
        $this->meta = [];
        $this->permalink = new Permalink();

        $defaultData = array_merge([
            'date'             => DateTime::createFromFormat('U', $this->getMTime()),
            'pretty_permalink' => true,
        ], $data);

        preg_match('/^(\d{4}-\d{2}-\d{2})-(.*)/', $this->getBasename('.'.$this->getExtension()),
            $matches);
        if (count($matches) === 3) {
            $defaultData['date'] = new DateTime($matches[1]);
            $defaultData['draft'] = false;
            $defaultData['slug'] = $matches[2];
            $defaultData['title'] = ucfirst(str_replace('-', ' ', $defaultData['slug']));
        }

        $this->setDataFromArray($defaultData);

        $this->setUid((! empty($this->getRelativePathname())) ? $this->getRelativePathname() : $this->getPathname());
    }

    /**
     * Get this files uid.
     *
     * @return string
     */
    public function getUid()
    {
        return $this->meta['uid'];
    }

    /**
     * Set this files uid.
     *
     * @param string$uid
     *
     * @return void
     */
    public function setUid($uid)
    {
        $uid = str_replace('.', '_', $uid);
        $uid = str_replace(['/', '\\'], '_', $uid);
        $this->meta['uid'] = $uid;
    }

    /**
     * Set this files data (via frontmatter or other source).
     *
     * @param array|string $key
     * @param null|mixed $value
     * @throws \Exception
     *
     * @return void
     */
    public function setData($key, $value = null)
    {
        if (is_array($key) && is_null($value)) {
            $this->setDataFromArray($key);

            return;
        }

        if ($key === 'date' && ! $value instanceof DateTime) {
            $date = new DateTime();
            if (! $unix = strtotime($value)) {
                if (! $unix = strtotime('@'.$value)) {
                    throw new \Exception('The date ['.$value.'] is in a format not supported by Tapestry.');
                }
            }
            $value = $date->createFromFormat('U', $unix);
        }

        if ($key === 'permalink') {
            $this->permalink->setTemplate($value);
        }

        $this->meta[$key] = $value;
    }

    /**
     * Set this files data from array source.
     *
     * @param array $data
     * @throws \Exception
     *
     * @return void
     */
    public function setDataFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
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
            return $this->meta;
        }
        if (! $this->hasData($key)) {
            return $default;
        }

        return $this->meta[$key];
    }

    /**
     * Return true if this file has data set for $key.
     *
     * @param $key
     *
     * @return bool
     */
    public function hasData($key)
    {
        return isset($this->meta[$key]);
    }

    /**
     * Get the content of the file that this object relates to.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getFileContent()
    {
        $content = file_get_contents($this->getPathname());
        if ($content !== false) {
            return $content;
        }

        throw new \Exception('Unable to read file ['.$this->getPathname().']');
    }

    /**
     * Set the files content, this should be excluding any frontmatter.
     *
     * @param $content
     */
    public function loadContent($content)
    {
        $this->content = $content;
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
            throw new \Exception('The file ['.$this->getRelativePathname().'] has not been loaded.');
        }

        return $this->content;
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

    /**
     * @param string $key
     * @param $value
     */
    public function setOverloaded(string $key, $value)
    {
        $this->overloaded[$key] = $value;
    }

    /**
     * Allows the overloading of the SplFileInfo filename property.
     *
     * @param bool $overload
     * @return mixed|string
     */
    public function getFilename(bool $overload = true)
    {
        if ($overload === true && isset($this->overloaded['filename'])) {
            return $this->overloaded['filename'];
        }

        return parent::getFilename();
    }

    /**
     * Allows the overloading of the SplFileInfo extension property.
     *
     * @param bool $overload
     * @return mixed|string
     */
    public function getExtension(bool $overload = true)
    {
        if ($overload === true && isset($this->overloaded['ext'])) {
            return $this->overloaded['ext'];
        }

        return parent::getExtension();
    }

    /**
     * A file can be considered loaded once its content property has been set, that way you know any frontmatter has
     * also been injected into the File objects data property.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->content !== false;
    }

    /**
     * Set by the Compile class when it passes this File through a Renderer.
     *
     * @param $value
     */
    public function setRendered(bool $value = true)
    {
        $this->rendered = $value;
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
     * @return bool
     */
    public function isToCopy() : bool
    {
        return $this->copy;
    }

    /**
     * @param bool $copy
     *
     * @return void
     */
    public function setToCopy(bool $copy)
    {
        $this->copy = $copy;
    }

    /**
     * @return bool
     */
    public function isBlocked() : bool
    {
        return $this->blocked;
    }

    /**
     * @param bool $blocked
     */
    public function setBlocked(bool $blocked = true)
    {
        $this->blocked = $blocked;
    }

    /**
     * @return bool
     */
    public function isIgnored(): bool
    {
        return $this->ignored;
    }

    /**
     * @param bool $ignored
     */
    public function setIgnored(bool $ignored = true)
    {
        $this->ignored = $ignored;
    }

    /////////////////////////////////////////////
    // Deprecated Methods
    /////////////////////////////////////////////

    /**
     * @deprecated
     * @throws \Exception
     */
    public function setFilename()
    {
        throw new \Exception('Deprecated Function [setFilename] used.');
    }

    /**
     * @deprecated
     * @throws \Exception
     */
    public function getExt()
    {
        throw new \Exception('Deprecated Function [getExt] used.');
    }

    /**
     * @deprecated
     * @throws \Exception
     */
    public function setExt()
    {
        throw new \Exception('Deprecated Function [setExt] used.');
    }

    /**
     * @deprecated
     * @throws \Exception
     */
    public function getLastModified()
    {
        throw new \Exception('Deprecated Function [getLastModified] used.');
    }

    /**
     * @deprecated
     * @throws \Exception
     */
    public function setLastModified()
    {
        throw new \Exception('Deprecated Function [setLastModified] used.');
    }
}
