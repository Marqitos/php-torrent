<?php
/**
 * This file is part of the Rodas\Torrent library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Rodas\Torrent
 * @subpackage Test
 * @copyright 2026 Marcos Porto <php@marcospor.to>
 * @license https://opensource.org/license/mit The MIT License
 * @link https://marcospor.to/repositories/torrent
 */

declare(strict_types=1);

namespace Rodas\Test\Torrent;

use PHPUnit\Framework\TestCase;
use Rodas\Torrent\BEncode\BEInt;
use Rodas\Torrent\BEncode\BEncodeType;

use function is_int;
use function is_string;
use function random_int;

use const PHP_EOL;
use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Test class for BEInt
 *
 * @covers Rodas\Torrent\BEncode\BEInt
 */
class BEIntTest extends TestCase {

    /**
     * Test BEInt::value
     *
     * @covers Rodas\Torrent\BEncode\BEInt::__construct
     * @covers Rodas\Torrent\BEncode\BEInt::value
     */
    public function testValue() {
        $initialValue = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $beInt = new BEInt($initialValue);
        $this->assertEquals($initialValue, $beInt->value);
        $this->assertTrue(is_int($beInt->value));
    }

    /**
     * Test BEInt::type
     *
     * @covers Rodas\Torrent\BEncode\BEInt::__construct
     * @covers Rodas\Torrent\BEncode\BEInt::type
     */
    public function testType() {
        $initialValue = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $beInt = new BEInt($initialValue);
        $this->assertEquals(BEncodeType::Int, $beInt->type);
        $this->assertTrue($beInt->type instanceof BEncodeType);
    }

    /**
     * Test BEInt::encode
     *
     * @covers Rodas\Torrent\BEncode\BEInt::__construct
     * @covers Rodas\Torrent\BEncode\BEInt::encode
     * @covers Rodas\Torrent\BEncode\BEInt::decode
     */
    public function testEncode() {
        $initialValue = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $beInt = new BEInt($initialValue);
        $encoded = $beInt->encode();
        $this->assertTrue(is_string($encoded));
        fwrite(STDERR, "BEInt: $initialValue, encoded as '$encoded'." . PHP_EOL);
        $beCopy = BEInt::decode($encoded);
        $this->assertEquals($initialValue, $beCopy->value);
    }

    /**
     * Test (string) BEInt
     *
     * @covers Rodas\Torrent\BEncode\BEInt::__construct
     * @covers Rodas\Torrent\BEncode\BEInt::encode
     * @covers Rodas\Torrent\BEncode\BEInt::__toString
     */
    public function testStringable() {
        $initialValue = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $beInt = new BEInt($initialValue);
        $encoded = $beInt->encode();
        $string = (string) $beInt;
        $this->assertEquals($encoded, $string);
    }

    /**
     * Test BEInt::serialize
     *
     * @covers Rodas\Torrent\BEncode\BEInt::__construct
     * @covers Rodas\Torrent\BEncode\BEInt::serialize
     * @covers Rodas\Torrent\BEncode\BEInt::unserialize
     * @covers Rodas\Torrent\BEncode\BEInt::__serialize
     * @covers Rodas\Torrent\BEncode\BEInt::__unserialize
     */
    public function testSerializable() {
        $initialValue = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $beInt = new BEInt($initialValue);
        $serialized = serialize($beInt);
        $this->assertTrue(is_string($serialized));
        fwrite(STDERR, "BEInt: $initialValue, serialized as '$serialized'." . PHP_EOL);
        $beUnserialized = unserialize($serialized);
        $this->assertEquals($initialValue, $beUnserialized->value);

        $methodSerialized = $beInt->serialize();
        $this->assertTrue(is_string($methodSerialized));
        fwrite(STDERR, "BEInt: $initialValue, serialize() as '$methodSerialized'." . PHP_EOL);
        $randomValue = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $beCopy = new BEInt($randomValue);
        $beCopy->unserialize($methodSerialized);
        $this->assertEquals($initialValue, $beCopy->value);
    }
}
