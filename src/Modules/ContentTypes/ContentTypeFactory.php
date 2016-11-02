<?php namespace Tapestry\Modules\ContentTypes;

use Tapestry\Entities\ContentType;

class ContentTypeFactory
{

    /**
     * Registered item stack
     *
     * @var array|ContentType[]
     */
    private $items = [];

    /**
     * Registered item lookup table
     *
     * @var array
     */
    private $lookupTable = [];


    /**
     * ContentTypeFactory constructor.
     * @param array|ContentType[] $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Add a ContentType to the registry
     *
     * @param ContentType $contentType
     * @param bool $overWrite should adding overwrite existing; if false an exception will be thrown if a matching collection already found
     * @throws \Exception
     */
    public function add(ContentType $contentType, $overWrite = false)
    {
        if (!$overWrite && $this->has($contentType->getPath())) {
            throw new \Exception('The collection [' . $this->lookupTable[$contentType->getPath()] . '] already collects for the path [' . $contentType->getPath() . ']');
        }
        $uid = sha1(md5(get_class($contentType)) . '_' . sha1($contentType->getName() . '-' . $contentType->getPath()));
        $this->items[$uid] = $contentType;
        $this->lookupTable[$contentType->getPath()] = $uid;
    }

    /**
     * Return true if the registry contains an absolute path
     *
     * @param string $path
     * @return bool
     */
    public function has($path)
    {
        return isset($this->lookupTable[$path]);
    }

    /**
     * Returns the absolute path from an input, so that you may then get the ContentType that deals with it.
     *
     * @param string $path
     * @return null|string
     */
    public function find($path)
    {
        foreach (array_keys($this->lookupTable) as $key) {
            if (starts_with($path, $key)) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Get ContentType by its supported path. If $path is '*' then if a default ContentType has been set it will be
     * returned.
     *
     * @param string $path
     * @return ContentType
     * @throws \Exception
     */
    public function get($path)
    {
        if ($path !== '*' && !$this->has($path)) {
            throw new \Exception('There is no collection that collects for the path [' . $path . ']');
        }
        if (isset($this->items[$this->lookupTable[$path]])) {
            return $this->items[$this->lookupTable[$path]];
        }
        return $this->items[$this->lookupTable['*']];
    }

}