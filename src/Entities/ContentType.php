<?php namespace Tapestry\Entities;

class ContentType
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @var string
     */
    private $path = '';

    /**
     * @var string
     */
    private $template = '';

    /**
     * @var
     */
    private $permalink;

    /**
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