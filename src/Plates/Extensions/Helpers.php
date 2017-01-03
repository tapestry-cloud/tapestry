<?php

namespace Tapestry\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class Helpers implements ExtensionInterface
{
    /** @var \Tapestry\Plates\Template */
    public $template;

    public function register(Engine $engine)
    {
        $engine->registerFunction('getFile', [$this, 'getFile']);
    }

    public function getFile()
    {
        return $this->template->getFile();
    }
}
