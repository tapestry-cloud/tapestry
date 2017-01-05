<?php

namespace Tapestry\Entities;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Entities\Generators\FileGenerator;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Modules\Renderers\ContentRendererFactory;

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
    private $permalink;

    /**
     * Which taxonomies used to classify this content types collection.
     *
     * @var array|Taxonomy[]
     */
    private $taxonomies = [];

    /**
     * Collection of Entities\File that this ContentType has collected.
     *
     * @var FlatCollection
     */
    private $items;

    /**
     * Cached output of getFileList. This is because two items with the same timestamp will end up randomly
     * swapping places with each other between calls to getFileList as happens with CollectionItemGenerator.
     *
     * @var null|array
     */
    private $itemsOrderCache = null;

    /**
     * ContentType constructor.
     *
     * @param string $name
     * @param array  $settings
     */
    public function __construct($name, array $settings)
    {
        $this->name = $name;

        $this->path = (isset($settings['path']) ? $settings['path'] : ('_'.$this->name));
        $this->template = (isset($settings['template']) ? $settings['template'] : $this->name);
        $this->permalink = (isset($settings['permalink']) ? $settings['permalink'] : ($this->name.'/{slug}.{ext}'));
        $this->enabled = (isset($settings['enabled']) ? boolval($settings['enabled']) : false);

        if (isset($settings['taxonomies'])) {
            foreach ($settings['taxonomies'] as $taxonomy) {
                $this->taxonomies[$taxonomy] = new Taxonomy($taxonomy, [
                    'template'  => $this->template.'/list-'.$taxonomy.'.phtml',
                    'permalink' => $this->name.'/'.$taxonomy.'/{?page}',
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
        $this->itemsOrderCache = null;
        $this->items->set($file->getUid(), $file->getData('date')->getTimestamp());

        foreach ($this->taxonomies as $taxonomy) {
            if ($classifications = $file->getData($taxonomy->getName())) {
                foreach ($classifications as $classification) {
                    $taxonomy->addFile($file, $classification);
                }
            }else{
                $file->setData([$taxonomy->getName() => []]);
            }
        }
    }

    public function hasFile(File $file)
    {
        return $this->items->has($file->getUid());
    }

    /**
     * @param string $name
     *
     * @return Taxonomy
     */
    public function getTaxonomy($name)
    {
        return $this->taxonomies[$name];
    }

    /**
     * Returns an ordered list of the file uid's that have been bucketed into this content type. The list is ordered by
     * the files date.
     *
     * @param string $order
     *
     * @return array
     */
    public function getFileList($order = 'desc')
    {
        if (! is_null($this->itemsOrderCache)) {
            return $this->itemsOrderCache;
        }

        // Order Files by date newer to older
        $this->items->sort(function ($a, $b) use ($order) {
            if ($a == $b) {
                return 0;
            }
            if ($order === 'asc') {
                return ($a < $b) ? -1 : 1;
            } else {
                return ($a > $b) ? -1 : 1;
            }
        });

        $this->itemsOrderCache = $this->items->all();

        return $this->itemsOrderCache;
    }

    /**
     * Mutate project files that are contained within this ContentType.
     *
     * @param Project $project
     */
    public function mutateProjectFiles(Project $project)
    {
        /** @var ContentRendererFactory $contentRenderers */
        $contentRenderers = $project->get('content_renderers');

        foreach (array_keys($this->getFileList()) as $fileKey) {
            /** @var File $file */
            if (! $file = $project->get('files.'.$fileKey)) {
                continue;
            }

            $file->setData(['content_type' => $this->name]);

            if ($this->permalink !== '*') {
                $file->setPermalink(new Permalink($this->permalink));
            }

            // If we are not a default Content Type
            $templatePath = $project->sourceDirectory.DIRECTORY_SEPARATOR.'_views'.DIRECTORY_SEPARATOR.$this->template.'.phtml';
            if ($this->template !== 'default' && file_exists($templatePath)) {
                $fileRenderer = $contentRenderers->get($file->getExt());
                $file->setData(['content' => $fileRenderer->render($file)]);
                $file->setFileInfo(new SplFileInfo($templatePath, '_views', '_views'.DIRECTORY_SEPARATOR.$this->template.'.phtml'));

                if ($fileRenderer->supportsFrontMatter()) {
                    $frontMatter = new FrontMatter($file->getFileContent());
                    $file->setData(array_merge_recursive($file->getData(), $frontMatter->getData()));
                    $file->setContent($frontMatter->getContent());
                } else {
                    $file->setContent($file->getFileContent());
                }

                if ($generator = $file->getData('generator')) {
                    $project->replaceFile($file, new FileGenerator($file));
                }
            }
        }
    }
}
