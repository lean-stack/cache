<?php

namespace Lean\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class CacheItemPool implements CacheItemPoolInterface
{
    /** @var \phpFastCache */
    protected $_cache;

    /**
     * The driver instance, not public available, but provided here for better code completion
     *
     * @var \BasePhpFastCache */
    protected $_instance;

    /**
     * CacheItemPool constructor.
     *
     * @param string $storage
     * @param array  $options
     */
    public function __construct($storage = '', $options = array())
    {
        $this->_cache = new \phpFastCache($storage,$options);
        $this->_instance = $this->_cache->instance;
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
     * @return \Psr\Cache\CacheItemInterface
     *                                       The corresponding Cache Item.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *                                             If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *                                             MUST be thrown.
     */
    public function getItem($key)
    {
        $cacheItem = new CacheItem($key,$this->_instance);
        return $cacheItem;
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys
     *                    An indexed array of keys of items to retrieve.
     *
     * @return array|\Traversable
     *                            A traversable collection of Cache Items keyed by the cache keys of
     *                            each item. A Cache item will be returned for each key, even if that
     *                            key is not found. However, if no keys are specified then an empty
     *                            traversable MUST be returned instead.
     */
    public function getItems(array $keys = array())
    {
        // TODO: Implement getItems() method.
        return [];
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *              True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        // TODO: Implement clear() method.
        return false;
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys
     *                    An array of keys that should be removed from the pool.
     *
     * @return static
     *                The invoked object.
     */
    public function deleteItems(array $keys)
    {
        // TODO: Implement deleteItems() method.
        return $this;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *                                 The cache item to save.
     *
     * @return static
     *                The invoked object.
     */
    public function save(CacheItemInterface $item)
    {
        $now = new \DateTime('now');
        $expiration = $item->getExpiration();
        $ttl = $expiration->getTimestamp() - $now->getTimestamp();
        $this->_instance->set($item->getKey(),$item->get(),$ttl);
        return $this;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *                                 The cache item to save.
     *
     * @return static
     *                The invoked object.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        // TODO: Implement saveDeferred() method.
        return $this;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *              TRUE if all not-yet-saved items were successfully saved. FALSE otherwise.
     */
    public function commit()
    {
        // TODO: Implement commit() method.
        return false;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *    The key for which to check existence.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *  True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        // TODO: Implement hasItem() method.
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key for which to delete
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        // TODO: Implement deleteItem() method.
    }
}
