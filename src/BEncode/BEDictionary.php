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

use Countable;
use Iterator;
use Rodas\Torrent\BEncode;
use ValueError;

class BEDictionary implements BEncodeDataInterface, Iterator, Countable {
    use IterableTrait;

# Constructor
    /**
     * Create a new instance of BEList
     *
     * @param  array $value list data
     */
    public function __construct(array $dictionary = []) {
        $this->value = [];
        foreach ($dictionary as $key => $item) {
            if (! is_string($key)) {
                return new ValueError("Key is not a string.");
            }
            if (! $this->set($key, $item)) {
                throw new ValueError();
            }
        }
        $this->iterator = $this->getIterator();
    }

# Members of BEncodeTypeInterface
    /**
     * @inheritDoc
     */
    public BEncodeType $type {
        get => BEncodeType::Dictionary;
    }
## -- Members of BEncodeTypeInterface

# Members of BEncodeDataInterface
    /**
     * Gets de BEncoded value
     *
     * @var array
     */
    public array $value;

    public static function decode(string &$raw, int &$offset = 0): BEncodeTypeInterface {
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
        $encoded = "d";
        foreach ($this->value as $key => $value) {
            $beKey = new BEString($key);
            $encoded .= $beKey->encode();
            $encoded .= $value->encode();
        }
        $encoded .= "e";
        return $encoded;
    }
# -- Members of BEncodeDataInterface

# Members of Serializable
    /**
     * Return data as string
     *
     * @return string
     */
    public function __unserialize(array $data): void {
        $this->value = [];
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        $this->iterator = $this->getIterator();
    }
    /**
     * Set value from string
     *
     * @param  string $data Serialized data
     * @return void
     */
    public function unserialize(string $data): void {
        $values = unserialize($data);
        $this->value = [];
        foreach ($values as $key => $item) {
            $this->set($key, $item);
        }
        $this->iterator = $this->getIterator();
    }
# -- Members of Serializable

# Members of IterableTrait
    protected function getIterator(): Iterator {
        foreach ($this->value as $key => $value) {
            yield $key => $value;
        }
    }
# -- Members of IterableTrait

    /**
     * Remove an item from the dictionary
     *
     * @param  string $key
     * @return void
     */
    public function remove(string $key) {
        unset($this->value[$key]);
    }

    /**
     * Add an item to the dictionary
     *
     * @param  string                                     $key  Key to add
     * @param  BEncodeDataInterface|int|string|array|bool $item Item to add
     * @return bool                                             true on success, false otherwise
     */
    public function set(string $key, BEncodeDataInterface|int|string|array|bool $item): bool {
        $item = BEncode::asBEncodeObject($item);
        if ($item instanceof BEncodeDataInterface) {
            $this->value[$key] = $item;
            ksort($this->value, SORT_STRING);
            return true;
        }
        return false;
    }
}
