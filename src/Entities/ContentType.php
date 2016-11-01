<?php namespace Tapestry\Entities;

class ContentType
{
    /**
     * The developer friendly name of this content type
     * @var string
     */
    private $name;

    /**
     * Is this content type allowed to collect from its $path
     * @var bool
     */
    private $enabled = false;

    /**
     * The path which this content type collects from
     * @var string
     */
    private $path = '';

    /**
     * The template path relative to the source path
     * @var string
     */
    private $template = '';

    /**
     * The permalink template for this content type. e.g. /%slug%.html
     * @var string
     */
    private $permalink;

    /**
     * Which taxonomies used to classify this content types collection
     * @var array
     */
    private $taxonomies = [];

    /**
     * ContentType constructor.
     * @param string $name
     * @param array $settings
     */
    public function __construct($name, array $settings)
    {
        $this->name = $name;

        $this->path = (isset($settings['path']) ? $settings['path'] : ('_' . $this->name));
        $this->template = (isset($settings['template']) ? $settings['template'] : $this->name);
        $this->permalink = (isset($settings['permalink']) ? $settings['permalink'] : ($this->name . '/%slug%.html'));
        $this->enabled = (isset($settings['enabled']) ? boolval($settings['enabled']) : false);

        if (isset($settings['taxonomies'])) {
            foreach ($settings['taxonomies'] as $taxonomy) {
                $this->taxonomies[$taxonomy] = []; // @todo needs a Collection class here for search/filter/order/pagination/etc?
            }
        }

        dd($this);
    }

    // ...
}