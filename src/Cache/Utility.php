<?php

namespace Lean\Cache;

class Utility
{
    /**
     * Validates a given key.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * Key - A string of at least one character that uniquely identifies a cached item.
     * Implementing libraries MUST support keys consisting of the characters A-Z, a-z, 0-9, _, and . in any
     * order in UTF-8 encoding and a length of up to 64 characters. Implementing libraries MAY support
     * additional characters and encodings or longer lengths, but must support at least that minimum.
     * Libraries are responsible for their own escaping of key strings as appropriate, but MUST be able
     * to return the original unmodified key string. The following characters are reserved for future
     * extensions and MUST NOT be supported by implementing libraries: {}()/\@:
     */
    public static function validateKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf('Invalid key. Key must be a string, you gave %s.',
                is_object($key) ? get_class($key) : gettype($key)));
        }

        $normalized = str_replace(['_', '.'], '', $key);
        if (!ctype_alnum($normalized)) {
            throw new InvalidArgumentException('Invalid key. Key MUST contain only chars according to PSR-6');
        }
    }
}
