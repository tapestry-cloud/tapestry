<?php

namespace Tapestry\Entities;

class Permalink
{
    /**
     * @var string
     */
    private $template;

    /**
     * Permalink constructor.
     *
     * @param string $template
     */
    public function __construct($template = '{path}/{filename}.{ext}')
    {
        $this->setTemplate($template);
    }

    /**
     *
     * @param $template
     */
    public function setTemplate($template)
    {
        // If you set your permalink to be /about or /abc/123 you expect that to be the url
        // this means "prettifying" by making the generated file /about/index.html or /abc/123/index.html
        if (strpos($template, '.') === false){
            $template .= '/index.{ext}';
        }
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Returns a compiled permalink path in string form.
     *
     * @param File $file
     * @param bool $pretty
     *
     * @return mixed|string
     */
    public function getCompiled(File $file, $pretty = true)
    {
        $output = $this->template;
        $output = str_replace('{ext}', $file->getExt(), $output);
        $output = str_replace('{filename}', $this->sluggify($file->getFilename()), $output);
        $output = str_replace('{path}', $file->getPath(), $output);

        /** @var \DateTime $date */
        if ($date = $file->getData('date')) {
            $output = str_replace('{year}', $file->getData('year', $date->format('Y')), $output);
            $output = str_replace('{month}', $file->getData('month', $date->format('m')), $output);
            $output = str_replace('{day}', $file->getData('day', $date->format('d')), $output);
        }


        /** @var Pagination $pagination */
        if ($pagination = $file->getData('pagination')) {
            if ($pagination instanceof Pagination) {
                if ($pagination->currentPage == 1) {
                    $page = 'index';
                } else {
                    $page = $pagination->currentPage;
                }
                $output = str_replace('{page}', $page, $output);
            }
        }

        $output = str_replace('{slug}', $file->getData('slug', $this->sluggify($file->getData('title', $file->getFilename()))), $output);

        if (substr($output, 0, 1) !== '/') {
            $output = '/'.$output;
        }

        if ($pretty === true && $file->getData('pretty_permalink', true)) {
            return $this->prettify($output);
        }

        return $output;
    }

    /**
     * Slugify the input string.
     *
     * @param string $text
     *
     * @return string
     */
    private function sluggify($text)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }

    /**
     * Prettify the permalink. This will make /blog/categories.html into /blog/categories/index.html so that the url will
     * become /blog/categories/ making it "pretty".
     *
     * @param string $text
     *
     * @return string
     */
    private function prettify($text)
    {
        if (strpos($this->template, '{ext}') !== false || strpos($text, '.') !== false) {
            // Check to see if the file is index.html already
            if (strpos($text, 'index.') === false) {
                $parts = explode('.', $text);
                $text = array_shift($parts);
                $ext = array_shift($parts);

                return $text.'/index.'.$ext;
            }
        }

        return $text;
    }
}
