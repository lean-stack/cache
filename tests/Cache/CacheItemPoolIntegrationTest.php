<?php

namespace Tests\Lean\Cache;

use Cache\IntegrationTests\CachePoolTest;
use Lean\Cache\CacheItemPool;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolIntegrationTest extends CachePoolTest
{
    /**
     * @return CacheItemPoolInterface that is used in the tests
     */
    public function createCachePool()
    {
        return new CacheItemPool(['storage' => 'auto', 'path' => dirname(dirname(__DIR__)).'/.tmp/']);
    }
}
