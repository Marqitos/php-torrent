<?php
/**
 * This file is part of the Rodas\Torrent library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Rodas\Torrent
 * @subpackage Test
 * @copyright 2025 Marcos Porto <php@marcospor.to>
 * @license https://opensource.org/license/mit The MIT License
 * @link https://marcospor.to/repositories/torrent
 */

declare(strict_types=1);

namespace Rodas\Test\Dotenvx;

use PHPUnit\Framework\TestCase;
use Rodas\Torrent\BEncode\BEString;
use Rodas\Torrent\BEncode\BEncodeType;

use function is_int;
use function is_string;
use function random_int;

use const PHP_EOL;
use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Test class for BEString
 *
 * @covers Rodas\Torrent\BEncode\BEString
 */
class BEStringTest extends TestCase {

    protected static function randomString(int $maxLength = 16): string {
        $length = random_int(1, $maxLength);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Test BEString::value
     *
     * @covers Rodas\Torrent\BEncode\BEString::__construct
     * @covers Rodas\Torrent\BEncode\BEString::value
     */
    public function testValue() {
        $initialValue = static::randomString();
        $beString = new BEString($initialValue);
        $this->assertEquals($initialValue, $beString->value);
        $this->assertTrue(is_string($beString->value));
    }

    /**
     * Test BEString::type
     *
     * @covers Rodas\Torrent\BEncode\BEString::__construct
     * @covers Rodas\Torrent\BEncode\BEString::type
     */
    public function testType() {
        $initialValue = static::randomString();
        $beString = new BEString($initialValue);
        $this->assertEquals(BEncodeType::String, $beString->type);
        $this->assertTrue($beString->type instanceof BEncodeType);
    }

    /**
     * Test BEString::encode
     *
     * @covers Rodas\Torrent\BEncode\BEString::__construct
     * @covers Rodas\Torrent\BEncode\BEString::encode
     * @covers Rodas\Torrent\BEncode\BEString::decode
     */
    public function testEncode() {
        $initialValue = static::randomString();
        $beString = new BEString($initialValue);
        $encoded = $beString->encode();
        $this->assertTrue(is_string($encoded));
        fwrite(STDERR, "BEString: $initialValue, encoded as '$encoded'." . PHP_EOL);
        $beCopy = BEString::decode($encoded);
        $this->assertEquals($initialValue, $beCopy->value);
    }

    /**
     * Test (string) BEString
     *
     * @covers Rodas\Torrent\BEncode\BEString::__construct
     * @covers Rodas\Torrent\BEncode\BEString::encode
     * @covers Rodas\Torrent\BEncode\BEString::__toString
     */
    public function testStringable() {
        $initialValue = static::randomString();
        $beString = new BEString($initialValue);
        $encoded = $beString->encode();
        $string = (string) $beString;
        $this->assertEquals($encoded, $string);
    }

    /**
     * Test BEString::serialize
     *
     * @covers Rodas\Torrent\BEncode\BEString::__construct
     * @covers Rodas\Torrent\BEncode\BEString::serialize
     * @covers Rodas\Torrent\BEncode\BEString::unserialize
     * @covers Rodas\Torrent\BEncode\BEString::__serialize
     * @covers Rodas\Torrent\BEncode\BEString::__unserialize
     */
    public function testSerializable() {
        $initialValue = static::randomString();
        $beString = new BEString($initialValue);
        $serialized = serialize($beString);
        $this->assertTrue(is_string($serialized));
        fwrite(STDERR, "BEString: $initialValue, serialized as '$serialized'." . PHP_EOL);
        $beUnserialized = unserialize($serialized);
        $this->assertEquals($initialValue, $beUnserialized->value);

        $methodSerialized = $beString->serialize();
        $this->assertTrue(is_string($methodSerialized));
        fwrite(STDERR, "BEInt: $initialValue, serialize() as '$methodSerialized'." . PHP_EOL);
        $randomValue = static::randomString();
        $beCopy = new BEString($randomValue);
        $beCopy->unserialize($methodSerialized);
        $this->assertEquals($initialValue, $beCopy->value);
    }
}
