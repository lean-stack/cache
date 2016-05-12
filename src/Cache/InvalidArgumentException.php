<?php

namespace Lean\Cache;

class InvalidArgumentException extends CacheException implements \Psr\Cache\InvalidArgumentException
{
    /**
     * InvalidArgumentException constructor.
     */
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
