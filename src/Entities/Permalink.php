<?php namespace Tapestry\Entities;

class Permalink
{
    /**
     * @var string
     */
    private $template;

    /**
     * Permalink constructor.
     * @param string $template
     */
    public function __construct($template = '{path}/{filename}.{ext}')
    {
        $this->setTemplate($template);
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getCompiled(File $file)
    {
        $output = $this->template;
        $output = str_replace('{ext}', $file->getExt(), $output);
        $output = str_replace('{filename}', $this->sluggify($file->getFilename()), $output);
        $output = str_replace('{path}', $file->getPath(), $output);

        /** @var \DateTime $date */
        if ($date = $file->getData('date')){
            $output = str_replace('{year}', $date->format('Y'), $output);
            $output = str_replace('{month}', $date->format('m'), $output);
            $output = str_replace('{day}', $date->format('d'), $output);
        }

        /** @var Pagination $pagination */
        if ($pagination = $file->getData('pagination')){
            if ($pagination->currentPage == 1) {
                $page = 'index';
            }else{
                $page = $pagination->currentPage;
            }
            $output = str_replace('{page}', $page, $output);
        }

        $output = str_replace('{slug}', $file->getData('slug', $this->sluggify($file->getData('title', $file->getFilename()))), $output);

        return $output;
    }

    /**
     * Slugify the input string
     * @param string $text
     * @return string
     */
    private function sluggify($text) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
}