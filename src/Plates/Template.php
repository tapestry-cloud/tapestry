<?php namespace Tapestry\Plates;

use League\Plates\Template\Template as PlatesTemplate;
use LogicException;
use Tapestry\Entities\File;

/**
 * Class Template
 *
 * @todo maybe overload the Template class so that we can filter out frontmatter from phtml files before they are rendered and inject into the files data any front matter
 * The above may get complex if we are talking about data generators... but it may work in a nice, simple, compact way.
 *
 * @package Tapestry\Providers\Plates
 */
class Template extends PlatesTemplate
{
    /**
     * Create new Template instance.
     * @param Engine $engine
     * @param string $name
     */
    public function __construct(Engine $engine, $name)
    {
        parent::__construct($engine, $name);
    }

    /**
     * Returns the content for a section block.
     * @param  string      $name    Section name
     * @param  string      $default Default section content
     * @return string|null
     */
    protected function section($name, $default = null)
    {
        if ($name === 'content' && !isset($this->sections['content']) && isset($this->data['content'])){
            return $this->data['content'];
        }

        if (!isset($this->sections[$name])) {
            return $default;
        }

        return $this->sections[$name];
    }

    /**
     * Render the File
     * @param File $file
     * @param $tmpDirectory
     * @return string
     * @throws \Exception
     */
    public function renderFile(File $file, $tmpDirectory)
    {

        if ($layoutName = $file->getData('layout')) {
            $this->layoutName = (!strpos('_templates', $layoutName)) ? '_templates' . DIRECTORY_SEPARATOR . $layoutName : $layoutName;
            $this->layoutData = $file->getData();
        }

        try {
            $tmpPathName = $tmpDirectory . DIRECTORY_SEPARATOR . time() . '-' . sha1($file->getUid()) . '.phtml';
            file_put_contents($tmpPathName, $file->getContent());

            $this->data($file->getData());

            extract($this->data);

            ob_start();

            include $tmpPathName;

            $content = ob_get_clean();

            if (isset($this->layoutName)) {
                $layout = $this->engine->make($this->layoutName);
                $layout->sections = array_merge($this->sections, array('content' => $content));
                $content = $layout->render($this->layoutData);
            }

            return $content;
        } catch (LogicException $e) {
            if (ob_get_length() > 0) {
                ob_end_clean();
            }

            throw $e;
        }
    }
}