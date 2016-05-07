<?php

namespace Tests\Lean\Cache;

use Lean\Cache\CacheItemPool;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $pool;

    protected function setUp()
    {
        $this->pool = new CacheItemPool();
    }


    public function testItImplementsCacheItemPoolInterface()
    {
        $this->assertInstanceOf(CacheItemPoolInterface::class, $this->pool);
    }

    public function testGetItemReturnsCacheItemInterface()
    {
        $key = uniqid();
        $item = $this->pool->getItem($key);
        $this->assertInstanceOf(CacheItemInterface::class, $item);
    }
}
