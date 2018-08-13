<?php

namespace Tapestry\Modules\Source;

use DateTime;
use Tapestry\Entities\DependencyGraph\Node;
use Tapestry\Entities\Permalink;

abstract class AbstractSource implements SourceInterface, Node
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
     * Has the file been passed through a Renderer?
     *
     * @var bool
     */
    protected $rendered = false;

    /**
     * Should this file be copied from source to destination?
     * Files marked for copying will not be rendered.
     *
     * @var bool
     */
    protected $copy = false;

    /**
     * Because all files in the source tree are loaded, this ignore flag
     * is set on those that should not be parsed. This allows them to be
     * analysed for dependencies (e.g. a not ignored file depends upon a
     * ignored file).
     *
     * @var bool
     */
    protected $ignored = false;

    /**
     * Overloaded properties, in the case of SplFileSource these may be
     * SplFileInfo methods being overloaded.
     *
     * @var array
     */
    protected $overloaded = [];

    /**
     * The source files that depend upon this source.
     *
     * @var array Node[]|AbstractSource[]
     */
    protected $edges = [];

    /**
     * Add a source that depends upon this source.
     *
     * @param Node $node
     */
    public function addEdge(Node $node)
    {
        $this->edges[$node->getUid()] = $node;
    }

    /**
     * Return a list of source objects that depend upon this one.
     *
     * @return array
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

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
     * A file can be considered loaded once its content property
     * has been set, that way you know any front matter has also
     * been injected into the File objects data property.
     *
     * @return bool
     */
    public function hasContent(): bool
    {
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
     * Get the filename.
     * Without the file extension.
     *
     * @param bool $overloaded
     * @return string
     */
    public function getBasename(bool $overloaded = true): string
    {
        $e = explode('.', $this->getFilename($overloaded));
        array_pop($e);

        return implode('.', $e);
    }

    /**
     * Has the file changed since it was last processed?
     *
     * @return bool
     */
    public function hasChanged(): bool
    {
        return $this->hasChanged;
    }

    /**
     * Set the hasChanged flag.
     *
     * @param bool $value
     */
    public function setHasChanged(bool $value = true)
    {
        $this->hasChanged = $value;
    }

    /**
     * Has this source been through the renderer step?
     *
     * @return bool
     */
    public function isRendered(): bool
    {
        return $this->rendered;
    }

    /**
     * Set the rendered flag.
     *
     * @param bool $value
     */
    public function setRendered(bool $value = true)
    {
        $this->rendered = $value;
    }

    /**
     * Should the file be copied from source to dist, or processed?
     *
     * @return bool
     */
    public function isToCopy(): bool
    {
        return $this->copy;
    }

    /**
     * Set the copy flag.
     *
     * @param bool $value
     */
    public function setToCopy(bool $value = true)
    {
        $this->copy = $value;
    }

    /**
     * Is the source to be ignored by the compile steps?
     *
     * @return bool
     */
    public function isIgnored(): bool
    {
        return $this->ignored;
    }

    /**
     * Set the ignore flag.
     *
     * @param bool $value
     */
    public function setIgnored(bool $value = true)
    {
        $this->ignored = $value;
    }

    /**
     * Set the value of an overloaded property.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setOverloaded(string $key, $value)
    {
        $this->overloaded[$key] = $value;
    }

    /**
     * @param Node $node
     * @return bool
     */
    public function isSame(Node $node): bool
    {
        return true;
        // TODO: Implement isSame() method.
    }
}
