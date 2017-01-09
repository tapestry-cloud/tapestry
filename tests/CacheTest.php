<?php

namespace Tapestry\Tests;

use Tapestry\Entities\Cache;
use Tapestry\Entities\CacheStore;

class CacheTest extends CommandTestBase
{
    public function testCacheStoreValidateMethod()
    {
        $hashA = sha1('Hello World');
        $hashB = sha1('World Hello');

        $store = new CacheStore($hashA);
        $store->setItem('A', 'B');
        $store->setItem('B', 'C');
        $this->assertEquals(2, $store->count());

        $store->validate($hashA);
        $this->assertEquals(2, $store->count());

        $store = new CacheStore($hashA);
        $store->setItem('A', 'B');
        $store->setItem('B', 'C');
        $this->assertEquals(2, $store->count());

        $store->validate($hashB);
        $this->assertEquals(0, $store->count());
    }

    public function testCacheStoreSetGet()
    {
        $hashA = sha1('Hello World');

        $store = new CacheStore($hashA, '1.0.0');
        $store->setItem('A', 'B');
        $store->setItem('B', 'C');
        $store->setItem('D', true);
        $store->setItem('E', false);
        $store->setItem('F', null);

        $this->assertEquals('B', $store->getItem('A'));
        $this->assertEquals('C', $store->getItem('B'));
        $this->assertEquals(true, $store->getItem('D'));
        $this->assertEquals(false, $store->getItem('E'));
        $this->assertEquals(null, $store->getItem('F'));
        $this->assertEquals(null, $store->getItem('X'));
        $this->assertEquals(5, $store->count());
    }

    public function testCacheStoreReset()
    {
        $hashA = sha1('Hello World');

        $store = new CacheStore($hashA, '1.0.0');
        $store->setItem('A', 'B');
        $store->setItem('B', 'C');

        $this->assertEquals(2, $store->count());
        $store->reset();
        $this->assertEquals(0, $store->count());
    }

    public function testCacheInitialLoad()
    {
        $hashA = sha1('Hello World');
        $cache = new Cache(__DIR__ . '/_tmp/cache.bin', $hashA);

        $cache->load();
        $this->assertEquals(0, $cache->count());
    }

    public function testCacheSaveAndLoad()
    {
        $hashA = sha1('Hello World');
        $cache = new Cache(__DIR__ . '/_tmp/cache.bin', $hashA);

        $cache->setItem('A', 'B');
        $cache->setItem('B', 'C');
        $cache->setItem('X', false);
        $cache->setItem('Y', null);

        $this->assertEquals(4, $cache->count());

        $cache->save();

        $cache = new Cache(__DIR__ . '/_tmp/cache.bin', $hashA);
        $this->assertEquals(0, $cache->count());
        $cache->load();
        $this->assertEquals(4, $cache->count());

        $this->assertEquals('B', $cache->getItem('A'));
        $this->assertEquals('C', $cache->getItem('B'));
        $this->assertEquals(false, $cache->getItem('X'));
        $this->assertEquals(null, $cache->getItem('Y'));
        $this->assertEquals(null, $cache->getItem('Z'));
    }

    public function testCacheInvalidationByHash()
    {
        $hashA = sha1('Hello World');
        $hashB = sha1('World Hello');
        $cache = new Cache(__DIR__ . '/_tmp/cache.bin', $hashA);
        $cache->setItem('A', 'B');
        $cache->setItem('B', 'C');

        $this->assertEquals(2, $cache->count());

        $cache->save();

        $cache = new Cache(__DIR__ . '/_tmp/cache.bin', $hashB);
        $cache->load();
        $this->assertEquals(0, $cache->count());
    }
}
