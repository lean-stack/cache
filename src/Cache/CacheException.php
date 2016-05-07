<?php

namespace Lean\Cache;

class CacheException
    extends \Exception
    implements \Psr\Cache\CacheException
{

    /**
     * CacheException constructor.
     */
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}