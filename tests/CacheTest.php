<?php

namespace Tapestry\Tests;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Cache;
use Tapestry\Entities\CacheStore;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Entities\Project;
use Tapestry\Modules\Content\ReadCache;
use Tapestry\Modules\Content\WriteCache;

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

    public function testCacheReset()
    {
        $hashA = sha1('Hello World');
        $cache = new Cache(__DIR__ . '/_tmp/cache.bin', $hashA);
        $cache->setItem('A', 'B');
        $this->assertEquals(1, $cache->count());
        $cache->reset();
        $this->assertEquals(0, $cache->count());
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

    public function testReadCacheModule()
    {
        $this->copyDirectory('/assets/build_test_21/src', '/_tmp');

        $module = new ReadCache(new Finder());
        $project = new Project(__DIR__ . '/_tmp', 'test');

        $this->assertEquals(false, $project->has('cache'));

        $result = $module->__invoke($project, new NullOutput());

        $this->assertEquals(true, $project->has('cache'));
        $this->assertEquals(true, $result);
        $this->assertInstanceOf(Cache::class, $project->get('cache'));

        $project->get('cache')->setItem('A', 'B');
        $project->get('cache')->setItem('B', 'C');
        $this->assertEquals(2, $project->get('cache')->count());
        $project->get('cache')->save();

        // Reset $module && $project variables to test loading from save

        unset($module, $project);
        $module = new ReadCache(new Finder());
        $project = new Project(__DIR__ . '/_tmp', 'test');
        $module->__invoke($project, new NullOutput());
        $this->assertEquals(2, $project->get('cache')->count());
        $this->assertEquals('B', $project->get('cache')->getItem('A'));
        $this->assertEquals('C', $project->get('cache')->getItem('B'));

        // Reset $module && $project variables and modify the src directory to test cache invalidation
        unset($module, $project);

        self::$fileSystem->copy(
            __DIR__ . DIRECTORY_SEPARATOR . '/assets/build_test_21/src_replace/config.php',
            __DIR__ . DIRECTORY_SEPARATOR . '/_tmp/config.php',
            true
        );

        self::$fileSystem->copy(
            __DIR__ . DIRECTORY_SEPARATOR . '/assets/build_test_21/src_replace/kernel.php',
            __DIR__ . DIRECTORY_SEPARATOR . '/_tmp/kernel.php',
            true
        );

        $module = new ReadCache(new Finder());
        $project = new Project(__DIR__ . '/_tmp', 'test');
        $module->__invoke($project, new NullOutput());
        $this->assertEquals(0, $project->get('cache')->count());
    }

    public function testTemplateModificationInvalidateCache()
    {
        $this->copyDirectory('/assets/build_test_22/src', '/_tmp');
        $output = $this->runCommand('build', ['--quiet']);
        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__ . '/assets/build_test_22/check/index.html',
            __DIR__ . '/_tmp/build_local/test/index.html',
            '',
            true
        );

        self::$fileSystem->copy(
            __DIR__ . DIRECTORY_SEPARATOR . '/assets/build_test_22/src_replace/page.phtml',
            __DIR__ . DIRECTORY_SEPARATOR . '/_tmp/source/_templates/page.phtml',
            true
        );

        $output = $this->runCommand('build', ['--quiet']);
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__ . '/assets/build_test_22/check/index_replace.html',
            __DIR__ . '/_tmp/build_local/test/index.html',
            '',
            true
        );
    }
}
