<?php

namespace Tapestry\Modules\Source;

use DateTime;
use Tapestry\Entities\Permalink;

abstract class AbstractSource implements SourceInterface
{

    /**
     * File meta data, usually from front matter or site config.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * The Permalink object attached to this file.
     *
     * @var Permalink
     */
    protected $permalink;

    /**
     * This is the files rendered content as set by `setRenderedContent`.
     *
     * @var bool|string
     */
    protected $content = false;

    /**
     * Has the file changed since it was last loaded?
     *
     * @var bool
     */
    protected $hasChanged = false;

    /**
     * Get this sources uid.
     *
     * @return string
     */
    public function getUid(): string
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
    public function setUid(string $uid)
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
    public function setData(string $key, $value = null)
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
     * @param null|string $key
     * @param null $default
     *
     * @return array|mixed|null
     */
    public function getData(string $key = null, $default = null)
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
    public function hasData(string $key): bool
    {
        return isset($this->meta[$key]);
    }

    /**
     * A file can be considered loaded once its content property has been set, that way you know any frontmatter has
     * also been injected into the File objects data property.
     *
     * @return bool
     */
    public function hasContent(): bool{
        return $this->content !== false;
    }

    /**
     * Returns the file's Permalink.
     *
     * @return Permalink
     */
    public function getPermalink(): Permalink
    {
        return $this->permalink;
    }

    /**
     * Pretty Permalinks are disabled on all files that have their
     * permalink structure configured via front matter.
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function getCompiledPermalink(): string
    {
        $pretty = $this->getData('pretty_permalink', true);
        if ($this->hasData('permalink')) {
            $pretty = false;
        }

        return $this->permalink->getCompiled($this, $pretty);
    }

    public function hasChanged(): bool
    {
        return $this->hasChanged;
    }

    public function setHasChanged(bool $value = true)
    {
        $this->hasChanged = $value;
    }

}