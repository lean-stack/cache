<?php

namespace Tests\Lean\Cache;

use Lean\Cache\CacheItemPool;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolTest extends \PHPUnit_Framework_TestCase
{

    public function testItImplementsCacheItemPoolInterface()
    {
        $pool = new CacheItemPool();
        $this->assertInstanceOf(CacheItemPoolInterface::class, $pool);
    }
}
