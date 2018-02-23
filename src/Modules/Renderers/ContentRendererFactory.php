<?php

namespace Tapestry\Modules\Renderers;

use Exception;
use Tapestry\Entities\ProjectFile;
use Tapestry\Entities\Renderers\RendererInterface;

class ContentRendererFactory
{
    /**
     * Registered item stack.
     *
     * @var array|RendererInterface[]
     */
    private $items = [];

    /**
     * Registered item lookup table.
     *
     * @var array
     */
    private $lookupTable = [];

    /**
     * ContentRendererFactory constructor.
     *
     * @param array|RendererInterface[] $items
     * @throws Exception
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Return all Content Renderers registered with this factory.
     *
     * @return array|RendererInterface[]
     */
    public function all()
    {
        return array_values($this->items);
    }

    /**
     * @param RendererInterface $item
     * @param bool              $overWrite
     *
     * @throws Exception
     */
    public function add(RendererInterface $item, $overWrite = false)
    {
        $uid = sha1(get_class($item).'_'.sha1(json_encode($item->supportedExtensions())));

        foreach ($item->supportedExtensions() as $ext) {
            if ($this->has($ext) && ! $overWrite) {
                throw new Exception('The collection ['. 1 .'] already collects for the path ['. 1 .']');
            }
        }

        $this->items[$uid] = $item;

        foreach ($item->supportedExtensions() as $ext) {
            $this->lookupTable[$ext] = $uid;
        }
    }

    /**
     * Return true if the registry contains an extension.
     *
     * @param string $ext
     *
     * @return bool
     */
    public function has($ext)
    {
        return isset($this->lookupTable[$ext]);
    }

    /**
     * @param $ext
     *
     * @throws Exception
     *
     * @return RendererInterface
     */
    public function get($ext)
    {
        if (! $this->has($ext) && ! $this->has('*')) {
            throw new Exception('There is no collection that collects for the extension ['.$ext.']');
        }

        if (! $this->has($ext) && $this->has('*')) {
            return $this->items[$this->lookupTable['*']];
        }

        return $this->items[$this->lookupTable[$ext]];
    }

    /**
     * Identify which Renderer to be used and then execute it upon the file in question.
     *
     * @param ProjectFile $file
     * @throws Exception
     */
    public function renderFile(ProjectFile &$file)
    {
        if ($file->isRendered()) {
            return;
        }
        $fileRenderer = $this->get($file->getExtension());
        $file->loadContent($fileRenderer->render($file));
        $file->setOverloaded('ext', $fileRenderer->getDestinationExtension($file->getExtension()));
        $file->setRendered(true);
        $fileRenderer->mutateFile($file);
    }
}
