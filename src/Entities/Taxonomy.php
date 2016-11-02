<?php namespace Tapestry\Entities;

class Taxonomy
{
    /**
     * The developer friendly name of this content type
     * @var string
     */
    private $name;

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
     * Collection of Entities\File that this Taxonomy has collected
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

        $this->template = (isset($settings['template']) ? $settings['template'] : $this->name . '.phtml');
        $this->permalink = (isset($settings['permalink']) ? $settings['permalink'] : ($this->name . '/{page}'));

        $this->items = new Collection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function addFile(File $file, $classification){
        $this->items->set($classification . '.' . $file->getUid(), true);
    }

    // ...
}