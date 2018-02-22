<?php

namespace Tapestry\Plates;

use Exception;
use Throwable;
use LogicException;
use Tapestry\Entities\ProjectFile;
use League\Plates\Template\Template as PlatesTemplate;

/**
 * Class Template.
 * @deprecated
 */
class Template extends PlatesTemplate
{
    /**
     * Instance of the template engine.
     * @var Engine
     */
    protected $engine;

    /**
     * @var ProjectFile|null
     */
    private $file = null;

    /**
     * Create new Template instance.
     *
     * @param Engine $engine
     * @param string $name
     */
    public function __construct(Engine $engine, $name)
    {
        parent::__construct($engine, $name);
    }

    /**
     * Returns the content for a section block.
     *
     * @param string $name    Section name
     * @param string $default Default section content
     *
     * @return string|null
     */
    public function section($name, $default = null)
    {
        if ($name === 'content' && ! isset($this->sections['content']) && isset($this->data['content'])) {
            return $this->data['content'];
        }

        if (! isset($this->sections[$name])) {
            return $default;
        }

        return $this->sections[$name];
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(ProjectFile $file)
    {
        $this->file = $file;
    }

    /**
     * Render the ProjectFile.
     *
     * @param ProjectFile $file
     * @param array $data
     * @return string
     * @throws Exception
     * @throws Throwable
     */
    public function renderFile(ProjectFile $file, array $data = [])
    {
        $this->data($data);
        unset($data);

        $this->file = $file;
        $tmpDirectory = $this->engine->getProject()->currentWorkingDirectory.DIRECTORY_SEPARATOR.'.tmp';

        if ($layoutName = $file->getData('layout')) {
            $this->layoutName = (strpos('_templates', $layoutName) === false) ? '_templates'.DIRECTORY_SEPARATOR.$layoutName : $layoutName;
            $this->layoutData = $file->getData();
            $this->engine->getProject()->get('file_layout_cache')->merge([$this->file->getUid() => [$this->layoutName]]);
        }

        try {
            $tmpPathName = $tmpDirectory.DIRECTORY_SEPARATOR.time().'-'.sha1($file->getUid()).'.phtml';

            if (! file_exists($tmpDirectory)) {
                mkdir($tmpDirectory);
            }

            file_put_contents($tmpPathName, $file->getContent());

            $this->data($file->getData());
            $this->data([
                'permalink'     => $file->getCompiledPermalink(),
                'raw_permalink' => $file->getPermalink(),
            ]);

            extract($this->data);

            $level = ob_get_level();
            ob_start();
            include $tmpPathName;
            $content = ob_get_clean();

            if (isset($this->layoutName)) {
                $layout = $this->engine->make($this->layoutName);
                $layout->setFile($this->file);
                $layout->sections = array_merge($this->sections, ['content' => $content]);
                $content = $layout->render($this->layoutData);
            }

            return $content;
        } catch (Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }

    /**
     * Render the template and layout.
     * @param  array  $data
     * @throws \Throwable
     * @throws \Exception
     * @return string
     */
    public function render(array $data = [])
    {
        $this->data($data);
        unset($data);
        extract($this->data);

        if (! $this->exists()) {
            throw new LogicException(
                'The template "'.$this->name->getName().'" could not be found at "'.$this->path().'".'
            );
        }

        try {
            $level = ob_get_level();
            ob_start();

            include $this->path();

            $content = ob_get_clean();

            if (isset($this->layoutName)) {
                $layout = $this->engine->make($this->layoutName);
                $layout->setFile($this->file);
                $layout->sections = array_merge($this->sections, ['content' => $content]);
                $content = $layout->render($this->layoutData);
            }

            return $content;
        } catch (Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }

    /**
     * Set the template's layout.
     * @param  string $name
     * @param  array $data
     * @return void
     */
    public function layout($name, array $data = [])
    {
        $this->layoutName = $name;
        $this->layoutData = $data;
        $this->engine->getProject()->get('file_layout_cache')->merge([$this->file->getUid() => [$this->layoutName]]);
    }
}
