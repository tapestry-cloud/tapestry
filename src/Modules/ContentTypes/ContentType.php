<?php

namespace Tapestry\Modules\ContentTypes;

use Tapestry\Entities\Project;
use Tapestry\Entities\Taxonomy;
use Tapestry\Modules\Source\SourceInterface;

class ContentType
{
    /**
     * The developer friendly name of this content type.
     *
     * @var string
     */
    private $name;

    /**
     * Is this content type allowed to collect from its $path.
     *
     * @var bool
     */
    private $enabled = false;

    /**
     * The path which this content type collects from.
     *
     * @var string
     */
    private $path = '';

    /**
     * The template path relative to the source path.
     *
     * @var string
     */
    private $template = '';

    /**
     * The permalink template for this content type. e.g. /%slug%.html.
     *
     * @var string
     */
    private $permalink = '';

    /**
     * Which taxonomies used to classify this content types collection.
     *
     * @var array|Taxonomy[]
     */
    private $taxonomies = [];

    /**
     * Collection of SourceInterface that this ContentType has collected.
     *
     * @var array|SourceInterface[]
     */
    private $items = [];

    /**
     * Cached output of getSourceList. This is because two items with the same timestamp will end up randomly
     * swapping places with each other between calls to getSourceList as happens with CollectionItemGenerator.
     *
     * @var null|array
     */
    private $itemsOrderCache = null;

    /**
     * ContentType constructor.
     *
     * @param string $name
     * @param array $settings
     */
    public function __construct($name, array $settings)
    {
        $this->name = $name;

        $this->path = ((isset($settings['path']) && is_string($settings['path'])) ? $settings['path'] : ('_'.$this->name));
        $this->template = ((isset($settings['template']) && is_string($settings['template'])) ? $settings['template'] : '_templates'.DIRECTORY_SEPARATOR.$this->name);
        $this->permalink = ((isset($settings['permalink']) && is_string($settings['permalink'])) ? $settings['permalink'] : ($this->name.'/{slug}.{ext}'));
        $this->enabled = ((isset($settings['enabled']) && is_bool($settings['enabled'])) ? boolval($settings['enabled']) : false);

        // @todo for #31 look into this
        if (isset($settings['taxonomies'])) {
            foreach ($settings['taxonomies'] as $taxonomy) {
                $this->taxonomies[$taxonomy] = new Taxonomy($taxonomy);
            }
        }
    }

    /**
     * Returns the name assigned to this content type on __construct.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the path assigned to this content type on __construct.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the template assigned to this content type on __construct.
     *
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getPermalink(): string
    {
        return $this->permalink;
    }

    /**
     * Returns whether this content type is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Disable/Enable this content type.
     *
     * @param bool $value
     */
    public function setEnabled(bool $value = true)
    {
        $this->enabled = $value;
    }

    /**
     * Retrieve a Taxonomy by name.
     *
     * @param string $name
     * @return mixed|Taxonomy
     */
    public function getTaxonomy(string $name): Taxonomy
    {
        return $this->taxonomies[$name];
    }

    /**
     * Returns all Taxonomy configured for this content type.
     *
     * @return array|Taxonomy[]
     */
    public function getTaxonomies(): array
    {
        return $this->taxonomies;
    }

    /**
     * Assign SourceInterface to this content type.
     *
     * @param SourceInterface $source
     * @throws \Exception
     */
    public function addSource(SourceInterface $source)
    {
        $source->setData('contentType', $this->name);
        $this->itemsOrderCache = null;

        $this->items[$source->getUid()] = $source->getData('date')->getTimestamp();

        foreach ($this->taxonomies as $taxonomy) {
            if ($classifications = $source->getData($taxonomy->getName())) {
                foreach ($classifications as $classification) {
                    $taxonomy->addFile($source, $classification);
                }
            } else {
                $source->setData($taxonomy->getName(), []);
            }
        }
    }

    /**
     * Returns true if SourceInterface has been assigned to this content type.
     *
     * @param SourceInterface $source
     * @return bool
     */
    public function hasSource(SourceInterface $source): bool
    {
        return isset($this->items[$source->getUid()]);
    }

    /**
     * Returns an ordered list of the source uid's that have been bucketed into
     * this content type. The list is ordered by the files date.
     *
     * @param string $order
     * @throws \Exception
     * @return SourceInterface[]
     */
    public function getSourceList(string $order = 'desc')
    {
        if (! in_array(strtolower($order), ['asc', 'desc'])) {
            throw new \Exception('The order attribute of getSourceList must be either asc or desc');
        }

        // @todo finish
    }

    public function mutateProjectSources(Project $project)
    {
        // @todo finish
    }
}
