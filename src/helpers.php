<?php

if (!function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param string|object $class
     *
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('str_contains')) {
    /**
     * Determine if a given string contains a given sub-string.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('str_slug')) {
    /**
     * @param $str
     * @param string $delimiter
     *
     * @return mixed|string
     */
    function str_slug($str, $delimiter = '-')
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $str)));
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die.
     *
     * @param mixed $dump
     */
    function dd($dump)
    {
        var_dump($dump);
        die();
    }
}

if (!function_exists('starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    function starts_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('url')) {
    function url($uri = '')
    {
        /** @var Tapestry\Entities\Url $url */
        $url = \Tapestry\Tapestry::getInstance()->getContainer()->get(\Tapestry\Entities\Url::class);

        return $url->parse($uri);
    }
}

if (!function_exists('file_size_convert')) {
    function file_size_convert($size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[intval($i)];
    }
}
