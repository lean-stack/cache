<?php

namespace Lean\Cache;

use Psr\Cache\CacheItemInterface;

abstract class CacheItemState
{
    const DETACHED = 0;
    const ATTACHED = 1;
    const MODIFIED = 2;
}

class CacheItem implements CacheItemInterface
{
    /** @var \BasePhpFastCache */
    protected $_pool;

    /** @var string */
    protected $_key;

    /** @var mixed */
    protected $_value;

    /** @var int */
    protected $_state;

    /** @var bool|null */
    protected $_hit;

    /** @var int */
    protected $_ttl;

    /** @var \DateTime */
    protected $_expiresAt;

    /**
     * CacheItem constructor.
     *
     * @param string            $key
     * @param \BasePhpFastCache $pool
     *
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     */
    public function __construct($key, \BasePhpFastCache $pool)
    {
        if ($key === null) {
            throw new InvalidArgumentException('Cache key may not be null');
        }
        if ($pool === null) {
            throw new InvalidArgumentException('You must provide the current pool');
        }

        $this->_key = $key;
        $this->_pool = $pool;
        $this->_state = CacheItemState::DETACHED;
    }

    /**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string
     *                The key string for this cache item.
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * The value returned must be identical to the value original stored by set().
     *
     * if isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *               The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        if ($this->_state == CacheItemState::DETACHED) {
            $this->_loadValue();
        }

        return $this->_value;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * Implementing Libraries MAY provide a default TTL if one is not specified.
     * If no TTL is specified and no default TTL has been set, the TTL MUST
     * be set to the maximum possible duration of the underlying storage
     * mechanism, or permanent if possible.
     *
     * @param mixed $value
     *                     The serializable value to be stored.
     *
     * @return static
     *                The invoked object.
     */
    public function set($value)
    {
        $this->_value = $value;
        $this->_state = CacheItemState::MODIFIED;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return bool
     *              True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit()
    {
        if ($this->_state == CacheItemState::DETACHED) {
            $this->_loadValue();
        }

        return $this->_hit;
    }

    /**
     * Confirms if the cache item exists in the cache.
     *
     * Note: This method MAY avoid retrieving the cached value for performance
     * reasons, which could result in a race condition between exists() and get().
     * To avoid that potential race condition use isHit() instead.
     *
     * @return bool
     *              True if item exists in the cache, false otherwise.
     */
    public function exists()
    {
        return $this->_pool->isExisting($this->getKey());
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface $expiration
     *                                       The point in time after which the item MUST be considered expired.
     *                                       If null is passed explicitly, a default value MAY be used. If none is set,
     *                                       the value should be stored permanently or for as long as the
     *                                       implementation allows.
     *
     * @return static
     *                The called object.
     */
    public function expiresAt($expiration)
    {
        $this->_expiresAt = $expiration;

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval $time
     *                                The period of time from the present after which the item MUST be considered
     *                                expired. An integer parameter is understood to be the time in seconds until
     *                                expiration.
     *
     * @return static
     *                The called object.
     */
    public function expiresAfter($time)
    {
        if ($time instanceof \DateInterval) {
            $zero = new \DateTime('@0');
            $this->_ttl = $zero->add($time)->getTimestamp();
        } else {
            $this->_ttl = $time;
        }

        return $this;
    }

    /**
     * Returns the expiration time of a not-yet-expired cache item.
     *
     * If this cache item is a Cache Miss, this method MAY return the time at
     * which the item expired or the current time if that is not available.
     *
     * @return \DateTime
     *                   The timestamp at which this cache item will expire.
     */
    public function getExpiration()
    {
        if ($this->_expiresAt !== null) {
            return $this->_expiresAt;
        }

        if ($this->_ttl !== null) {
            $then = new \DateTime('now');
            $then->modify('+'.$this->_ttl.' sec');

            return $then;
        }

        $then = new \DateTime('now + 5 year');

        return $then;
    }

    /**
     * Loads the value from the cache pool into the item store.
     *
     * Sideeffect: ensures a valid hit state
     */
    private function _loadValue()
    {
        $this->_value = $this->_pool->get($this->_key);
        $this->_hit = $this->_pool->isExisting($this->_key);
        $this->_state = CacheItemState::ATTACHED;
    }
}
