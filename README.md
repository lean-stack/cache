# LeanCache [![Build Status][travis-image]][travis-url]
> A PSR-6 cache implementation

This PSR-6 implementation is a wrapper to the phpfastcache library (http://www.phpfastcache.com/).
To create a CacheItemPool you have to pass the phpfastcache configuration as constructor
arguments. Using the files driver for example this will be:

    $pool = new CacheItemPool(['storage' => 'files', 'path' => dirname(dirname(__DIR__)).'/.tmp/']);

[travis-image]: https://travis-ci.org/lean-stack/cache.svg?branch=master
[travis-url]: https://travis-ci.org/lean-stack/cache

