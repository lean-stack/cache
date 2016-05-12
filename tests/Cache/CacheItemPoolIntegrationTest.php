<?php

namespace Tests\Lean\Cache;

use Cache\IntegrationTests\CachePoolTest;
use Lean\Cache\CacheItemPool;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolIntegrationTest extends CachePoolTest
{
    /**
     * @return CacheItemPoolInterface that is used in the tests
     */
    public function createCachePool()
    {
        return new CacheItemPool(['storage' => 'files', 'path' => dirname(dirname(__DIR__)) . '/.tmp/']);
    }
}
