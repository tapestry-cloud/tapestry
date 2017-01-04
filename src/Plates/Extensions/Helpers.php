<?php

namespace Tapestry\Plates\Extensions;

use League\Plates\Engine;
use Tapestry\Entities\File;
use Tapestry\Entities\ViewFileTrait;
use League\Plates\Extension\ExtensionInterface;

class Helpers implements ExtensionInterface
{
    use ViewFileTrait;

    /**
     * @var \Tapestry\Plates\Template
     */
    public $template;

    /**
     * Register extension function.
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('getFile', [$this, 'getFile']);
        $class = new \ReflectionClass(ViewFileTrait::class);
        foreach ($class->getMethods() as $method) {
            if ($method->name === 'getFile') {
                continue;
            }
            $engine->registerFunction($method->name, [$this, $method->name]);
        }
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->template->getFile();
    }
}
