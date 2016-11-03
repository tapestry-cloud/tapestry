<?php namespace Tapestry\Entities\Generators;

use Tapestry\Entities\File;
use Tapestry\Entities\ProjectFileInterface;

class FileGenerator implements ProjectFileInterface
{
    /**
     * @var File
     */
    private $file;

    /**
     * FileGenerator constructor.
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function generate()
    {
        $n = 1;
    }

    public function __call($name, $arguments)
    {
        if (! method_exists($this, $name) && method_exists($this->file, $name)) {
            return call_user_func_array([$this->file, $name], $arguments);
        }
    }
}