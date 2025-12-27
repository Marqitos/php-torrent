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

namespace Rodas\Torrent\BEncode;

use Rodas\Torrent\BEncode;

class BEDictionary implements BEncodeDataInterface, Iterator, Countable {
    use IterableTrait;

    protected function __construct(array $dictionary) {
        $this->value = $dictionary;
        $this->generator = $this->getGenerator();
    }

# Members of BEncodeTypeInterface
    public BEncodeType $type {
        get => BEncodeType::Dictionary;
    };
## -- Members of BEncodeTypeInterface

# Members of BEncodeDataInterface
    public array $value {
        get => $this->value;
        protected set => $this->value = $value;
    }

    public static function decode(&$raw, &$offset): BEncodeTypeInterface {
        $dictionary = [];
        $error = null;
        while (true) {
            // Read key
            $offset++;
            $name = BEncode::decode($raw, $offset);
            if ($name->type == BEncodeType::End) {
                break;
            } elseif ($name->type == BEncodeType::Error) {
                $error = $name;
            } elseif ($name->type != BEncodeType::String) {
                $error = new BEncode_Error("Key name in dictionary was not a string.");
            }

            if ($error !== null) {
                return $error;
            }

            // Read value
            $offset++;
            $value = BEncode::decode($raw, $offset);
            if ($name->type == BEncodeType::End) {
                $error = new Error("Missing value for a key: " . $name->value);
            } elseif ($value->type == BEncodeType::Error) {
                $error = $value;
            } elseif (isset($dictionary[$name->value])) {
                $error = new Error("Duplicate key in dictionary: " . $name->value);
            }

            if ($error !== null) {
                return $error;
            }

            $dictionary[$name->value] = $value;
        }
        return new static($dictionary);
    }

    public function encode(): string {
        ksort($this->value, SORT_STRING);
        $encoded = "d";
        foreach ($this->value as $key => $value) {
            $bstr = new BEString($key);
            $encoded .= $bstr->encode();
            $encoded .= $value->encode();
        }
        $encoded .= "e";
        return $encoded;
    }
# -- Members of BEncodeDataInterface

# Members of Serializable
    public function __unserialize(array $data): void {
        $this->value = [];
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
# -- Members of Serializable

    protected function getGenerator(): Generator {
        foreach ($this->value as $key => $value) {
            yield $key => $value;
        }
    }

    public static function fromArray(array $dictionary): BEncodeTypeInterface {
        foreach ($dictionary as $key => &$value) {
            if (is_string($key)) {
                return new Error("Key is not a string.");
            }
            if (!($value instanceof BEncodeDataInterface)) {
                $value = BEncode::asBEncodeObject($value);
            }
        }
        return new static($dictionary);
    }

    public function remove(string $key) {
        unset($this->value[$key]);
    }

    public function set(string $key, BEncodeDataInterface|int|string|array|bool $item): bool {
        $item = BEncode::asBEncodeObject($item);
        if ($item instanceof BEncodeDataInterface) {
            $this->value[$key] = $item;
            return true;
        }
        return false;
    }
}
