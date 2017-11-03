<?php

namespace Tapestry\Entities;

use Composer\Semver\Comparator;
use Tapestry\Exceptions\InvalidVersionException;
use Tapestry\Tapestry;

class Cache
{
    /**
     * @var CacheStore
     */
    private $store;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $hash;

    /**
     * Cache constructor.
     *
     * @param string $path
     * @param string $hash
     */
    public function __construct($path, $hash)
    {
        clearstatcache();
        $this->path = $path;
        $this->hash = $hash;
        $this->store = new CacheStore($this->hash, Tapestry::VERSION);
    }

    public function load()
    {
        if (file_exists($this->path)) {
            $this->store = unserialize(file_get_contents($this->path));
            $this->store->validate($this->hash);
            if (is_null($this->store->getTapestryVersion())){
                $this->store->setTapestryVersion(Tapestry::VERSION);
            } else {
                // If Tapestry version is older than that used to generate the project then throw an error.
                // Else update the stored tapestry version.
                if (Comparator::greaterThan($this->store->getTapestryVersion(), Tapestry::VERSION)){
                    throw new InvalidVersionException('This project was last generated with Tapestry version ['. $this->store->getTapestryVersion() .'], you are compiling with an outdated version.');
                }else{
                    $this->store->setTapestryVersion(Tapestry::VERSION);
                }
            }
        }
    }

    public function save()
    {
        file_put_contents($this->path, serialize($this->store));
    }

    public function setCacheStore(CacheStore $cacheStore) {
        $this->store = $cacheStore;
    }

    public function setItem($key, $value)
    {
        $this->store->setItem($key, $value);
    }

    public function getItem($key)
    {
        return $this->store->getItem($key);
    }

    public function count()
    {
        return $this->store->count();
    }

    public function reset()
    {
        $this->store->reset();
    }
}
