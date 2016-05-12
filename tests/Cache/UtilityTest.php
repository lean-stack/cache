<?php

namespace Tests\Lean\Cache;

use Lean\Cache\InvalidArgumentException;
use Lean\Cache\Utility;

class UtilityTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidCharsInKeys()
    {
        $invalidChars = '{}()/\@:';

        for ($i = 0; $i < strlen($invalidChars); ++$i) {
            $char = $invalidChars[$i];
            $key = uniqid($char);
            try {
                Utility::validateKey($key);
                $this->assertTrue(false, sprintf('Key %s should be invalid', $key));
            } catch (\Exception $ex) {
                $this->assertInstanceOf(InvalidArgumentException::class, $ex);
            }
        }
    }

    public function testNullKey()
    {
        $this->expectException(InvalidArgumentException::class);
        Utility::validateKey(null);
    }

    public function testEmptyKey()
    {
        $this->expectException(InvalidArgumentException::class);
        Utility::validateKey('');
    }

    public function testWhitespaceKeys()
    {
        $this->expectException(InvalidArgumentException::class);
        Utility::validateKey('   ');
    }

    public function testNonStringKey()
    {
        $this->expectException(InvalidArgumentException::class);
        Utility::validateKey(new \stdClass());
    }
}
