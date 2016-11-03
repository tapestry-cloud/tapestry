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
     * @var array|Taxonomy[]
     */
    private $taxonomies = [];

    /**
     * Collection of Entities\File that this ContentType has collected
     * @var Collection
     */
    private $items;

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
        $this->permalink = (isset($settings['permalink']) ? $settings['permalink'] : ($this->name . '/{slug}.{ext}'));
        $this->enabled = (isset($settings['enabled']) ? boolval($settings['enabled']) : false);

        if (isset($settings['taxonomies'])) {
            foreach ($settings['taxonomies'] as $taxonomy) {
                $this->taxonomies[$taxonomy] = new Taxonomy($taxonomy, [
                    'template' => $this->template . '/list-' . $taxonomy . '.phtml',
                    'permalink' => $this->name . '/' . $taxonomy . '/{?page}'
                ]);
            }
        }

        $this->items = new FlatCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function addFile(File $file)
    {
        $this->items->set($file->getUid(), $file->getData('date')->getTimestamp());

        foreach ($this->taxonomies as $taxonomy) {
            if ($classifications = $file->getData($taxonomy->getName())) {
                foreach ($classifications as $classification) {
                    $taxonomy->addFile($file, $classification);
                }
            }
        }
    }

    public function hasFile(File $file)
    {
        return $this->items->has($file->getUid());
    }

    public function getFileList($order = 'desc')
    {
        // Order Files by date newer to older
        $this->items->sort(function($a, $b) use ($order){
            if ($a == $b) {
                return 0;
            }
            if ($order === 'asc'){
                return ($a < $b) ? -1 : 1;
            }else{
                return ($a > $b) ? -1 : 1;
            }
        });

        return $this->items->all();
    }
}