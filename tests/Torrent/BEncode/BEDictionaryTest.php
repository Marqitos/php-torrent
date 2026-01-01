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
use Rodas\Torrent\BEncode\BEDictionary;
use Rodas\Torrent\BEncode\BEncodeType;

use function array_is_list;
use function count;
use function is_string;

use const PHP_EOL;

/**
 * Test class for BEDictionary
 *
 * @covers Rodas\Torrent\BEncode\BEDictionary
 */
class BEDictionaryTest extends TestCase {

    /**
     * Test BEDictionary::value
     *
     * @covers Rodas\Torrent\BEncode\BEDictionary::__construct
     * @covers Rodas\Torrent\BEncode\BEDictionary::value
     * @covers Rodas\Torrent\BEncode\BEDictionary::current
     * @covers Rodas\Torrent\BEncode\BEDictionary::key
     * @covers Rodas\Torrent\BEncode\BEDictionary::next
     */
    public function testValue() {
        $initialValue = [
            'foo'   => 'bar',
            'baz'   => 5];
        $beDictionary = new BEDictionary($initialValue);
        $this->assertEquals(count($initialValue), count($beDictionary));
        ksort($initialValue, SORT_STRING);
        foreach ($initialValue as $key => $item) {
            $this->assertEquals($key, $beDictionary->key());
            $this->assertEquals($item, $beDictionary->current()->value);
            $beDictionary->next();
        }
        foreach ($beDictionary as $key => $item) {
            $this->assertTrue(isset($initialValue[$key]));
            $this->assertEquals($initialValue[$key], $item->value);
        }
        $this->assertFalse(array_is_list($beDictionary->value));
    }

    /**
     * Test BEDictionary::type
     *
     * @covers Rodas\Torrent\BEncode\BEDictionary::__construct
     * @covers Rodas\Torrent\BEncode\BEDictionary::type
     */
    public function testType() {
        $initialValue = [
            'foo'   => 'bar',
            'baz'   => 5];
        $beDictionary = new BEDictionary($initialValue);
        $this->assertTrue($beDictionary->type instanceof BEncodeType);
        $this->assertEquals(BEncodeType::Dictionary, $beDictionary->type);
    }

    /**
     * Test BEDictionary::encode
     *
     * @covers Rodas\Torrent\BEncode\BEDictionary::__construct
     * @covers Rodas\Torrent\BEncode\BEDictionary::encode
     * @covers Rodas\Torrent\BEncode\BEDictionary::decode
     */
    public function testEncode() {
        $initialValue = [
            'foo'   => 'bar',
            'baz'   => 5];
        $beDictionary = new BEDictionary($initialValue);
        $encoded = $beDictionary->encode();
        $this->assertTrue(is_string($encoded));
        fwrite(STDERR, 'BEDictionary: ' . serialize($initialValue) . ", encoded as '$encoded'." . PHP_EOL);
        $beCopy = BEDictionary::decode($encoded);
        ksort($initialValue, SORT_STRING);
        foreach ($initialValue as $key => $item) {
            $this->assertEquals($key, $beDictionary->key());
            $this->assertEquals($item, $beDictionary->current()->value);
            $beDictionary->next();
        }
        foreach ($beDictionary as $key => $item) {
            $this->assertTrue(isset($initialValue[$key]));
            $this->assertEquals($initialValue[$key], $item->value);
        }
        $this->assertEquals(count($initialValue), count($beCopy));
        $this->assertFalse(array_is_list($beCopy->value));
    }

    /**
     * Test (string) BEDictionary
     *
     * @covers Rodas\Torrent\BEncode\BEDictionary::__construct
     * @covers Rodas\Torrent\BEncode\BEDictionary::encode
     * @covers Rodas\Torrent\BEncode\BEDictionary::__toString
     */
    public function testStringable() {
        $initialValue = [
            'foo'   => 'bar',
            'baz'   => 5];
        $beDictionary = new BEDictionary($initialValue);
        $encoded = $beDictionary->encode();
        $string = (string) $beDictionary;
        $this->assertEquals($encoded, $string);
    }

    /**
     * Test BEDictionary::serialize
     *
     * @covers Rodas\Torrent\BEncode\BEDictionary::__construct
     * @covers Rodas\Torrent\BEncode\BEDictionary::count
     * @covers Rodas\Torrent\BEncode\BEDictionary::serialize
     * @covers Rodas\Torrent\BEncode\BEDictionary::unserialize
     * @covers Rodas\Torrent\BEncode\BEDictionary::__serialize
     * @covers Rodas\Torrent\BEncode\BEDictionary::__unserialize
     */
    public function testSerializable() {
        $initialValue = [
            'foo'   => 'bar',
            'baz'   => 5];
        $beDictionary = new BEDictionary($initialValue);
        $serialized = serialize($beDictionary);
        $this->assertTrue(is_string($serialized));
        fwrite(STDERR, 'BEDictionary: ' . serialize($initialValue) . ", serialized as '$serialized'." . PHP_EOL);
        $beUnserialized = unserialize($serialized);
        ksort($initialValue, SORT_STRING);
        foreach ($initialValue as $key => $item) {
            $this->assertEquals($key, $beDictionary->key());
            $this->assertEquals($item, $beDictionary->current()->value);
            $beDictionary->next();
        }
        foreach ($beDictionary as $key => $item) {
            $this->assertTrue(isset($initialValue[$key]));
            $this->assertEquals($initialValue[$key], $item->value);
        }
        $this->assertEquals(count($initialValue), count($beUnserialized));
        $this->assertFalse(array_is_list($beUnserialized->value));

        $methodSerialized = $beDictionary->serialize();
        $this->assertTrue(is_string($methodSerialized));
        fwrite(STDERR, 'BEDictionary: ' . serialize($initialValue) . ", serialize() as '$methodSerialized'." . PHP_EOL);
        $beCopy = new BEDictionary();
        $beCopy->unserialize($methodSerialized);
        foreach ($initialValue as $key => $item) {
            $this->assertEquals($key, $beCopy->key());
            $this->assertEquals($item, $beCopy->current()->value);
            $beCopy->next();
        }
        foreach ($beCopy as $key => $item) {
            $this->assertTrue(isset($initialValue[$key]));
            $this->assertEquals($initialValue[$key], $item->value);
        }
        $this->assertEquals(count($initialValue), count($beCopy));
        $this->assertFalse(array_is_list($beCopy->value));
    }

    /**
     * Test BEDictionary::set
     *
     * @covers Rodas\Torrent\BEncode\BEDictionary::__construct
     * @covers Rodas\Torrent\BEncode\BEDictionary::set
     * @covers Rodas\Torrent\BEncode\BEDictionary::value
     */
    public function testSet() {
        $initialValue = [
            'foo'   => 'bar',
            'baz'   => 5];
        $beDictionary = new BEDictionary($initialValue);
        $count = count($initialValue);
        $this->assertEquals($count, count($beDictionary));
        $beDictionary->set('baz', 10);
        $this->assertEquals($count, count($beDictionary));
        $this->assertEquals(10, $beDictionary->value['baz']->value);
        $beDictionary->set('min', 0);
        $count++;
        $this->assertEquals($count, count($beDictionary));
        $this->assertEquals(0, $beDictionary->value['min']->value);
    }

    /**
     * Test BEDictionary implements Countable
     *
     * @covers Rodas\Torrent\BEncode\BEDictionary::__construct
     * @covers Rodas\Torrent\BEncode\BEDictionary::set
     * @covers Rodas\Torrent\BEncode\BEDictionary::value
     */
    public function testEmpty() {
        $beDictionary = new BEDictionary;
        $this->assertTrue(empty($beDictionary->value));
        $beDictionary->set('foo', 'baz');
        $this->assertFalse(empty($beDictionary->value));
        $this->assertEquals('baz', $beDictionary->value['foo']->value);
    }
}
