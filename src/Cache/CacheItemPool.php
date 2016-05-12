<?php

namespace Lean\Cache;

use phpFastCache\CacheManager;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Class CacheItemPool.
 *
 * CacheItemPool generates CacheItemInterface objects.
 *
 * The primary purpose of Cache\CacheItemPoolInterface is to accept a key from
 * the Calling Library and return the associated Cache\CacheItemInterface object.
 * It is also the primary point of interaction with the entire cache collection.
 * All configuration and initialization of the Pool is left up to an
 * Implementing Library.
 */
class CacheItemPool implements CacheItemPoolInterface
{
    /** @var \phpFastCache\Core\DriverAbstract */
    private $_cache;

    /** @var CacheItem[] */
    private $_queue = [];

    /**
     * CacheItemPool constructor.
     *
     * @param array  $config
     * @param string $method
     */
    public function __construct(array $config, $method = 'normal')
    {
        CacheManager::setup($config);
        CacheManager::CachingMethod($method);
        $this->_cache = CacheManager::getInstance();
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *                    The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException
     *                                  If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *                                  MUST be thrown.
     *
     * @return CacheItemInterface
     *                            The corresponding Cache Item.
     */
    public function getItem($key)
    {
        Utility::validateKey($key);

        if (isset($this->_queue[$key])) {
            return $this->_queue[$key];
        }

        $value = null;
        $isHit = $this->_cache->isExisting($key);
        if ($isHit) {
            $value = $this->_cache->get($key);
            if ($value instanceof NullObject) {
                $value = null;
            }
        }

        $item = new CacheItem();
        $item->setKey($key);
        $item->set($value);
        $item->setIsHit($isHit);

        return $item;
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys
     *                    An indexed array of keys of items to retrieve.
     *
     * @throws InvalidArgumentException
     *                                  If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *                                  MUST be thrown.
     *
     * @return array|\Traversable
     *                            A traversable collection of Cache Items keyed by the cache keys of
     *                            each item. A Cache item will be returned for each key, even if that
     *                            key is not found. However, if no keys are specified then an empty
     *                            traversable MUST be returned instead.
     */
    public function getItems(array $keys = array())
    {
        foreach ($keys as $key) {
            Utility::validateKey($key);
        }

        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *                    The key for which to check existence.
     *
     * @throws InvalidArgumentException
     *                                  If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *                                  MUST be thrown.
     *
     * @return bool
     *              True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        Utility::validateKey($key);

        if (isset($this->_queue[$key])) {
            return true;
        }

        return $this->_cache->isExisting($key);
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *              True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        $this->_queue = [];
        $this->_cache->clean();

        return true;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *                    The key for which to delete
     *
     * @throws InvalidArgumentException
     *                                  If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *                                  MUST be thrown.
     *
     * @return bool
     *              True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        Utility::validateKey($key);

        if (isset($this->_queue[$key])) {
            unset($this->_queue[$key]);
        }

        $this->_cache->delete($key);

        return true;
    }
    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys
     *                    An array of keys that should be removed from the pool.
     *
     * @throws InvalidArgumentException
     *                                  If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *                                  MUST be thrown.
     *
     * @return bool
     *              True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            Utility::validateKey($key);
        }

        foreach ($keys as $key) {
            $this->_cache->delete($key);
        }

        // TODO: Spec is somewhat not clear. What to return if one item could not be deleted?
        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *                                 The cache item to save.
     *
     * @return bool
     *              True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            return false;
        }

        // remove from queue
        if (isset($this->_queue[$item->getKey()])) {
            unset($this->_queue[$item->getKey()]);
        }

        /** @var CacheItem $cacheItem */
        $cacheItem = $item;

        // delete an expired item
        $expiry = $cacheItem->getExpiry();
        if ($expiry !== 0 && $expiry < time()) {
            $this->deleteItem($cacheItem->getKey());

            return true;
        }

        $this->_cache->set(
            $item->getKey(),
            $item->get() === null ? new NullObject() : $item->get(),
            $expiry === 0 ? 0 : $expiry - time()
        );

        return true;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *                                 The cache item to save.
     *
     * @return bool
     *              False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        /** @var CacheItem $cacheItem */
        $cacheItem = $item;
        if ($cacheItem->getExpiry() !== 0 && $cacheItem->getExpiry() < time()) {
            return true;
        }

        $this->_queue[$item->getKey()] = $item;

        /** @var CacheItem $cacheItem */
        $cacheItem = $item;
        $cacheItem->setIsHit(true);
        $cacheItem->setState(CacheItemState::DEFERRED);

        // any deferred value saved?
        if ($cacheItem->isProbablyDeferredValueSet()) {
            $cacheItem->setValue($cacheItem->getDeferredValue());
            $cacheItem->setProbablyDeferredValueSet(false);
        }

        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *              True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        foreach ($this->_queue as $key => $item) {
            $this->save($item);
        }
        $this->_queue = [];

        return true;
    }

    /**
     * commit queued items.
     */
    public function __destruct()
    {
        $this->commit();
    }
}
