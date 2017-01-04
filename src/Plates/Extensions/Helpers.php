<?php

namespace Tapestry\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Tapestry\Entities\File;
use Tapestry\Entities\ViewFileTrait;

class Helpers implements ExtensionInterface
{

    use ViewFileTrait;

    /**
     * @var \Tapestry\Plates\Template
     */
    public $template;

    /**
     * @var File
     */
    private $file;

    /**
     * Register extension function.
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('getFile', [$this, 'getFile']);
        $class = new \ReflectionClass(ViewFileTrait::class);
        foreach($class->getMethods() as $method) {
            if ($method->name === 'getFile'){ continue; }
            $engine->registerFunction($method->name, [$this, $method->name]);
        }
    }

    /**
     * @return File
     */
    public function getFile()
    {
        if (is_null($this->file)) {
            $this->file = $this->template->getFile();
        }

        return $this->file;
    }
}
