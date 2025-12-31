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

namespace Rodas\Test\Torrent;

use PHPUnit\Framework\TestCase;
use Rodas\Torrent\BEncode\BEList;
use Rodas\Torrent\BEncode\BEncodeType;

use function array_is_list;
use function count;
use function is_string;

use const PHP_EOL;

/**
 * Test class for BEList
 *
 * @covers Rodas\Torrent\BEncode\BEList
 */
class BEListTest extends TestCase {

    /**
     * Test BEList::value
     *
     * @covers Rodas\Torrent\BEncode\BEList::__construct
     * @covers Rodas\Torrent\BEncode\BEList::value
     */
    public function testValue() {
        $initialValue = ["foo", "bar", 5];
        $beList = new BEList($initialValue);
        $this->assertEquals(count($initialValue), count($beList));
        foreach ($initialValue as $item) {
            $this->assertEquals($item, $beList->current()->value);
            $beList->next();
        }
        $count = 0;
        foreach ($beList as $item) {
            $this->assertEquals($initialValue[$count], $item->value);
            $count++;
        }
        $this->assertTrue(array_is_list($beList->value));
    }

    /**
     * Test BEList::type
     *
     * @covers Rodas\Torrent\BEncode\BEList::__construct
     * @covers Rodas\Torrent\BEncode\BEList::type
     */
    public function testType() {
        $initialValue = ["foo", "bar", 5];
        $beList = new BEList($initialValue);
        $this->assertEquals(BEncodeType::List, $beList->type);
        $this->assertTrue($beList->type instanceof BEncodeType);
    }

    /**
     * Test BEList::encode
     *
     * @covers Rodas\Torrent\BEncode\BEList::__construct
     * @covers Rodas\Torrent\BEncode\BEList::encode
     * @covers Rodas\Torrent\BEncode\BEList::decode
     */
    public function testEncode() {
        $initialValue = ["foo", "bar", 5];
        $beList = new BEList($initialValue);
        $encoded = $beList->encode();
        $this->assertTrue(is_string($encoded));
        fwrite(STDERR, 'BEList: ' . serialize($initialValue) . ", encoded as '$encoded'." . PHP_EOL);
        $beCopy = BEList::decode($encoded);
        foreach ($initialValue as $item) {
            $this->assertEquals($item, $beList->current()->value);
            $beList->next();
        }
        $count = 0;
        foreach ($beList as $item) {
            $this->assertEquals($initialValue[$count], $item->value);
            $count++;
        }
        $this->assertEquals(count($initialValue), count($beCopy));
        $this->assertTrue(array_is_list($beCopy->value));
    }

    /**
     * Test (string) BEList
     *
     * @covers Rodas\Torrent\BEncode\BEList::__construct
     * @covers Rodas\Torrent\BEncode\BEList::encode
     * @covers Rodas\Torrent\BEncode\BEList::__toString
     */
    public function testStringable() {
        $initialValue = ["foo", "bar", 5];
        $beList = new BEList($initialValue);
        $encoded = $beList->encode();
        $string = (string) $beList;
        $this->assertEquals($encoded, $string);
    }

    /**
     * Test BEList::serialize
     *
     * @covers Rodas\Torrent\BEncode\BEList::__construct
     * @covers Rodas\Torrent\BEncode\BEList::serialize
     * @covers Rodas\Torrent\BEncode\BEList::unserialize
     * @covers Rodas\Torrent\BEncode\BEList::__serialize
     * @covers Rodas\Torrent\BEncode\BEList::__unserialize
     */
    public function testSerializable() {
        $initialValue = ["foo", "bar", 5];
        $beList = new BEList($initialValue);
        $serialized = serialize($beList);
        $this->assertTrue(is_string($serialized));
        fwrite(STDERR, 'BEList: ' . serialize($initialValue) . ", serialized as '$serialized'." . PHP_EOL);
        $beUnserialized = unserialize($serialized);
        foreach ($initialValue as $item) {
            $this->assertEquals($item, $beList->current()->value);
            $beList->next();
        }
        $count = 0;
        foreach ($beList as $item) {
            $this->assertEquals($initialValue[$count], $item->value);
            $count++;
        }
        $this->assertEquals(count($initialValue), count($beUnserialized));
        $this->assertTrue(array_is_list($beUnserialized->value));

        $methodSerialized = $beList->serialize();
        $this->assertTrue(is_string($methodSerialized));
        fwrite(STDERR, 'BEList: ' . serialize($initialValue) . ", serialize() as '$methodSerialized'." . PHP_EOL);
        $beCopy = new BEList();
        $beCopy->unserialize($methodSerialized);
        foreach ($initialValue as $item) {
            $this->assertEquals($item, $beCopy->current()->value);
            $beCopy->next();
        }
        $count = 0;
        foreach ($beCopy as $item) {
            $this->assertEquals($initialValue[$count], $item->value);
            $count++;
        }
        $this->assertEquals(count($initialValue), count($beCopy));
        $this->assertTrue(array_is_list($beCopy->value));
    }

    /**
     * Test BEList::add
     *
     * @covers Rodas\Torrent\BEncode\BEList::__construct
     * @covers Rodas\Torrent\BEncode\BEList::add
     * @covers Rodas\Torrent\BEncode\BEList::value
     */
    public function testAdd() {
        $initialValue = ["foo", "bar", 5];
        $beList = new BEList($initialValue);
        $count = count($initialValue);
        $this->assertEquals($count, count($beList));
        $beList->add("baz");
        $count++;
        $this->assertEquals($count, count($beList));
        $this->assertEquals("baz", $beList->value[3]->value);
    }

    /**
     * Test BEList implements Countable
     *
     * @covers Rodas\Torrent\BEncode\BEList::__construct
     * @covers Rodas\Torrent\BEncode\BEList::add
     * @covers Rodas\Torrent\BEncode\BEList::value
     */
    public function testEmpty() {
        $beList = new BEList;
        $this->assertTrue(empty($beList->value));
        $beList->add("foo");
        $this->assertFalse(empty($beList->value));
        $this->assertEquals("foo", $beList->value[0]->value);
    }
}
