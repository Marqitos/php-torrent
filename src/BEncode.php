<?php
/**
 * This file is part of the Rodas\Torrent library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Rodas\Torrent
 * @copyright 2025 Marcos Porto <php@marcospor.to>
 * @license https://opensource.org/license/mit The MIT License
 * @link https://marcospor.to/repositories/torrent
 */

declare(strict_types=1);

namespace Rodas\Torrent;

use Rodas\Torrent\BEncode\BEncodeTypeInterface;
use Rodas\Torrent\BEncode\Error;
use Rodas\Torrent\BEncode\BEInt;
use Rodas\Torrent\BEncode\BEList;
use Rodas\Torrent\BEncode\BEString;

class BEncode {
    public static function decode(string &$raw, int &$offset = 0): BEncodeTypeInterface {
        if (strlen($raw) <= $offset) {
            return new Error("Decoder exceeded max length.");
        }

        $char = $raw[$offset];
        switch ($char) {
            case "i":
                return BEInt::decode($raw, $offset);
            case "d":
                return BEDictionary::decode($raw, $offset);
            case "l":
                return BEList::decode($raw, $offset);
            case "e":
                return new End();
            case "0":
            case is_numeric($char):
                return BEString::decode($raw, $offset);
            default:
                return new Error("Decoder encountered unknown char '" . $char . "' at offset " . $offset . ".");
        }
    }

    /**
     * @param int|string|array $value
     * @throws Exception
     * @return string
     */
    public static function encode(int|string|array|bool $value): string {
        if (is_bool($value)) { // Convert to int
            $value = $value ? 1 : 0;
        }
        $result = '';
        if (is_array($value)) {
            if (array_is_list($value)) {

                foreach ($value as $v) {
                    $result .= self::encode($v);
                }

                return "l{$result}e";
            } else {
                ksort($value, SORT_STRING);
                foreach ($value as $k => $v) {
                    $result .= self::encode("$k") . self::encode($v);
                }

                $result = "d{$result}e";
            }
        } elseif (is_int($value)) {
            $result = "i{$value}e";
        } elseif (is_string($value)) {
            $result = strlen($value) . ":$value";
        }
        if (!empty($result)) {
            return $result;
        }

        $type = gettype($value);
        throw new Exception("Bencode supports only integers, strings and arrays. $type given.");
    }

    public static function asBEncodeObject($value) {
        if ($value instanceof BEncodeTypeInterface) {
            return $value;
        }
        if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }
        if (is_int($value)) {
            $value = new BEInteger($value);
        } elseif (is_array($value)) {
            if (array_is_list($value)) {
                $value = BEList::fromArray($value);
            } else {
                $value = BEDictionary::fromArray($value);}
            }
        }
        if (!($value instanceof BEncodeDataInterface)) {
            return Error("Cannot convert value to BEncode object.");
        }
        return $value;
    }

}
