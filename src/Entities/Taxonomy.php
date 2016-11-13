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

        if (!$this->items->has($classification)){
            $this->items->set($classification, []);
        }

        $this->items->set($classification . '.' . $file->getUid(), $file->getData('date')->getTimestamp());
    }

    /**
     * Returns an ordered list of the file uid's that have been bucketed into this taxonomy. The list is ordered by
     * the files date.
     *
     * @param string $order
     * @return array
     */
    public function getFileList($order = 'desc')
    {
        // Order Files by date newer to older
        $this->items->sortMultiDimension(function($a, $b) use ($order){
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