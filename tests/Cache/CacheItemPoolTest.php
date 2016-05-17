<?php

namespace Tests\Lean\Cache;

use Lean\Cache\CacheItem;
use Lean\Cache\CacheItemPool;
use Lean\Cache\CacheItemState;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolTest extends \PHPUnit_Framework_TestCase
{
    /** @var CacheItemPoolInterface */
    private $_pool;

    protected function setUp()
    {
        $this->_pool = new CacheItemPool(['storage' => 'memcached']);
    }

    public function testClassImplementsInterface()
    {
        $this->assertInstanceOf(CacheItemPoolInterface::class, $this->_pool);
    }

    public function testGetReturnsCacheItem()
    {
        $key = uniqid();
        $item = $this->_pool->getItem($key);
        $this->assertInstanceOf(CacheItemInterface::class, $item);
    }

    public function testGetItemHasKey()
    {
        $key = uniqid();
        $item = $this->_pool->getItem($key);
        $this->assertEquals($key, $item->getKey());
    }

    public function testForeignCacheItem()
    {
        $item = new ForeignCacheItemImplementation();
        $this->assertFalse($this->_pool->save($item));
    }

    public function testDeferredStateOfItems()
    {
        $key = uniqid();
        $item = $this->_pool->getItem($key);
        $this->_pool->saveDeferred($item);

        /** @var CacheItem $cacheItem */
        $cacheItem = $item;
        $this->assertEquals(CacheItemState::DEFERRED, $cacheItem->getState());
    }
}
