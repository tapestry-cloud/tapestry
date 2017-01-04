<?php

namespace Tapestry\Entities;

/**
 * Class ViewFileTrait.
 *
 * This trait contains methods that are shared between the ViewFile class and Tapestry\Plates\Extensions\Helpers
 *
 * @see Tapestry\Plates\Extensions\Helpers
 * @see Tapestry\Entities\ViewFile
 */
trait ViewFileTrait
{
    /**
     * @return File
     */
    abstract public function getFile();

    /**
     * Returns data from attached File or $default if not found.
     *
     * @param $key
     * @param null $default
     * @return array|mixed|null
     */
    public function getData($key, $default = null)
    {
        return $this->getFile()->getData($key, $default);
    }

    /**
     * Returns the Files compiled permalink.
     *
     * @return mixed|string
     */
    public function getPermalink()
    {
        return $this->getFile()->getCompiledPermalink();
    }

    /**
     * Returns the Files compiled permalink as a absolute url.
     *
     * @return string
     */
    public function getUrl()
    {
        return url($this->getPermalink());
    }

    /**
     * Returns the Files date data, this equals the files last modified date unless date is set in front matter.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->getData('date');
    }

    /**
     * @param string $name
     * @return array
     */
    public function taxonomyList($name)
    {
        return $this->getData($name, []);
    }

    /**
     * Returns the files content, rendered or otherwise.
     *
     * @return string
     * @throws \Exception
     */
    public function getContent()
    {
        if ($content = $this->getData('content')) {
            return $content;
        }

        return $this->getFile()->getContent();
    }

    /**
     * Returns true if the page has $pagination set to an instance of Pagination.
     *
     * @return bool
     */
    public function isPaginated()
    {
        if (! $pagination = $this->getData('pagination')) {
            return false;
        }

        if (! $pagination instanceof Pagination) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if the page has $previous_next set as an instance of stdClass.
     *
     * @return bool
     */
    public function hasPreviousNext()
    {
        if (! $previousNext = $this->getData('previous_next')) {
            return false;
        }

        if (! $previousNext instanceof \stdClass) {
            return false;
        }

        return true;
    }
}
