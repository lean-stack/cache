<?php

namespace Lean\Cache;

use Psr\Cache\CacheItemInterface;

abstract class CacheItemState
{
    const IMMEDIATE = 0;
    const DEFERRED = 1;
}

/**
 * Class CacheItem.
 */
class CacheItem implements CacheItemInterface
{
    /** @var string */
    private $_key;

    /** @var mixed */
    private $_value;

    /** @var mixed */
    private $_isHit;

    /** @var int Unix timestamp with expiry time */
    private $_expiry = 0;

    /** @var int CacheItemState */
    private $_state = CacheItemState::IMMEDIATE;

    /** @var bool  */
    private $_probablyDeferredValueSet = false;

    /** @var mixed */
    private $_deferred_value = null;

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
     * The value returned must be identical to the value originally stored by set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *               The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        return $this->_value;
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
        return $this->_isHit;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * @param mixed $value
     *                     The serializable value to be stored.
     *
     * @return static
     *                The invoked object.
     */
    public function set($value)
    {
        if ($this->_state === CacheItemState::DEFERRED) {
            $this->_probablyDeferredValueSet = true;
            $this->_deferred_value = $value;
        } else {
            $this->_value = $value;
        }

        return $this;
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
     *
     * @throws InvalidArgumentException
     */
    public function expiresAt($expiration)
    {
        if ($expiration === null) {

            $this->_expiry = 0;

        } else {

            if( !$expiration instanceof \DateTimeInterface)
                throw new InvalidArgumentException('Invalid expiration time.');

            $this->_expiry = $expiration->getTimestamp();
        }

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval $time
     *                                The period of time from the present after which the item MUST be considered
     *                                expired. An integer parameter is understood to be the time in seconds until
     *                                expiration. If null is passed explicitly, a default value MAY be used.
     *                                If none is set, the value should be stored permanently or for as long as the
     *                                implementation allows.
     *
     * @return static The called object.
     *                The called object.
     *
     * @throws InvalidArgumentException
     */
    public function expiresAfter($time)
    {
        if ($time === null) {
            $this->_expiry = 0;
            return $this;
        }

        if (is_int($time)) {
            $now = new \DateTime('now');
            $this->_expiry = $now->modify('+' . $time . ' sec')->getTimestamp();
            return $this;
        }

        if (!$time instanceof \DateInterval)
            throw new InvalidArgumentException('Invalid expiration time.');

        $now = new \DateTime('now');
        $this->_expiry = $now->add($time)->getTimestamp();

        return $this;
    }

    /**
     * Returns the unix timestamp of the expiry.
     *
     * @return int|null
     */
    public function getExpiry()
    {
        return $this->_expiry;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->_key = $key;
    }

    /**
     * Sets the hit state.
     *
     * @param mixed $isHit
     */
    public function setIsHit($isHit)
    {
        $this->_isHit = $isHit;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->_state = $state;
    }

    /**
     * @return bool
     */
    public function isProbablyDeferredValueSet()
    {
        return $this->_probablyDeferredValueSet;
    }

    /**
     * @param bool $probablyDeferredValueSet
     */
    public function setProbablyDeferredValueSet($probablyDeferredValueSet)
    {
        $this->_probablyDeferredValueSet = $probablyDeferredValueSet;
    }

    /**
     * @return mixed
     */
    public function getDeferredValue()
    {
        return $this->_deferred_value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }
}
