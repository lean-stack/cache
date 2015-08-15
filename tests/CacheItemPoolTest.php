<?php

use Lean\Cache\CacheItemPool;

class CacheItemPoolTest extends PHPUnit_Framework_TestCase
{
    /** @var CacheItemPool */
    protected $cache;

    /**
     *
     */
    protected function setUp()
    {
        $options = \phpFastCache::$config;
        $options['path'] = __DIR__.'/tmp';

        if (!file_exists($options['path'])) {
            mkdir($options['path']);
        }

        $this->cache = new CacheItemPool('auto', $options);
    }

    public function testItemPoolReturnsInstanceOfItem()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $this->assertInstanceOf(\Psr\Cache\CacheItemInterface::class, $item);
    }

    public function testItemNotExisting()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $exists = $item->exists();
        $this->assertFalse($exists);
    }

    public function testSavingCacheItem()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $item->set(17);
        $this->cache->save($item);

        $item = $this->cache->getItem($key);
        $value = $item->get();

        $this->assertEquals(17, $value);
    }

    public function testExistingItemHasHit()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $item->set(17);
        $this->cache->save($item);

        $item = $this->cache->getItem($key);
        $this->assertTrue($item->isHit());
    }

    public function testNonExistingItemHasNoHit()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $this->assertFalse($item->isHit());
    }

    public function testNonExistingItemHasNullValue()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $value = $item->get();
        $this->assertNull($value);
    }

    public function testSavedItemWithNullValueExists()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $item->set(null);
        $this->cache->save($item);

        $item = $this->cache->getItem($key);
        $this->assertTrue($item->exists());
    }

    public function testSavedItemWithNullValueHasHit()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $this->cache->save($item);

        $item = $this->cache->getItem($key);
        $this->assertTrue($item->isHit());
    }

    public function testDefaultExpiration()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $this->assertTrue($item->getExpiration() > new DateTime());
    }

    public function testSettingExpirationDate()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $expiresAt = new DateTime('now + 10 min');
        $item->expiresAt($expiresAt);
        $this->assertEquals($expiresAt, $item->getExpiration());
    }

    public function testSettingExpirationSeconds()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $item->expiresAfter(600);
        $then = new DateTime('now + 600 sec');
        $this->assertEquals($then, $item->getExpiration());
    }

    public function testSettingExpirationDateInterval()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $interval = new DateInterval('PT10M');
        $then = new DateTime('now + 600 sec');
        $item->expiresAfter($interval);
        $this->assertEquals($then, $item->getExpiration());
    }

    public function testReadingExpiredItem()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $item->set(17);
        $item->expiresAfter(3);
        $this->cache->save($item);
        sleep(5);
        $item = $this->cache->getItem($key);
        $this->assertNull($item->get());
    }

    public function testReadingNonExpiredItem()
    {
        $key = uniqid();
        $item = $this->cache->getItem($key);
        $item->set(17);
        $item->expiresAfter(5);
        $this->cache->save($item);
        sleep(3);
        $item = $this->cache->getItem($key);
        $this->assertEquals(17, $item->get());
    }
}
