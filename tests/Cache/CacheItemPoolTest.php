<?php

namespace Tests\Lean\Cache;

use Lean\Cache\CacheItemPool;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolTest extends \PHPUnit_Framework_TestCase
{
    /** @var CacheItemPoolInterface */
    private $_pool;

    protected function setUp()
    {
        $this->_pool = new CacheItemPool(['storage' => 'auto', 'path' => dirname(dirname(__DIR__)).'/.tmp/']);
        var_dump($this->_pool); die(1);
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
}
