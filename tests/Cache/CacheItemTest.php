<?php

namespace Tests\Lean\Cache;

use Lean\Cache\CacheItem;

class CacheItemTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingAValue()
    {
        $value = uniqid();

        $item = new CacheItem();
        $item->set($value);

        $this->assertEquals($value, $item->get());
    }

    public function testSettingExpirationDate()
    {
        $expireDate = new \DateTime('now');
        $expireDate->modify('+ 60 sec');

        $item = new CacheItem();
        $item->expiresAt($expireDate);

        $this->assertEquals($expireDate->getTimestamp(), $item->getExpiry());
    }

    public function testSettingExpirationInterval()
    {
        $expireInterval = new \DateInterval('PT60S');
        $now = new \DateTime('now');
        $expiry = $now->add($expireInterval);

        $item = new CacheItem();
        $item->expiresAfter($expireInterval);

        $this->assertEquals($expiry->getTimestamp(), $item->getExpiry());
    }

    public function testSettingExpirationInSeconds()
    {
        $expireInterval = new \DateInterval('PT60S');
        $now = new \DateTime('now');
        $expiry = $now->add($expireInterval);

        $item = new CacheItem();
        $item->expiresAfter(60);

        $this->assertEquals($expiry->getTimestamp(), $item->getExpiry());
    }
}
