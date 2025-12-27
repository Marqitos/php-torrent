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
use Generator;
use Iterator;
use Rodas\Torrent\BEncode;
use ValueError;

/**
 * Represents a BEncoded list
 */
class BEList implements BEncodeDataInterface, Iterator, Countable {
    use IterableTrait;

# Constructor
    /**
     * Create a new instance of BEList
     *
     * @param  array $value list data
     */
    public function __construct(array $list = []) {
        $this->value = [];
        foreach ($list as $item) {
            if (!$this->add($item)) {
                throw new ValueError();
            }
        }
        $this->generator = $this->getGenerator();
    }
# -- Constructor

# Members of BEncodeTypeInterface
    /**
     * @inheritDoc
     */
    public BEncodeType $type {
        get => BEncodeType::List;
    }
## -- Members of BEncodeTypeInterface

# Members of BEncodeDataInterface
    /**
     * Gets de BEncoded value
     *
     * @var array
     */
    public array $value;
    /**
     * @inheritDoc
     */
    public static function decode(string &$raw, int &$offset = 0): BEncodeTypeInterface {
        $list = [];
        while (true) {
            $offset++;
            $value = BEncode::decode($raw, $offset);
            if ($value->type == BEncodeType::End) {
                break;
            }

            if ($value->type == BEncodeType::Error) {
                return $value;
            }

            $list[] = $value;
        }
        return new static($list);
    }
    /**
     * @inheritDoc
     */
    public function encode(): string {
        $encoded = "l";
        foreach ($this->value as $item)  {
            $encoded .= $item->encode();
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
        foreach ($data as $item) {
            $this->add($item);
        }
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
        foreach ($values as $item) {
            $this->add($item);
        }
    }

# -- Members of Serializable

# Members of IterableTrait
    protected function getGenerator(): Generator {
        foreach ($this->value as $value) {
            yield $value;
        }
    }
# -- Members of IterableTrait

    /**
     * Add an item to the list
     *
     * @param  BEncodeDataInterface|int|string|array|bool $item Item to add
     * @return bool                                             true on success, false otherwise
     */
    public function add(BEncodeDataInterface|int|string|array|bool $item): bool {
        $item = BEncode::asBEncodeObject($item);
        if ($item instanceof BEncodeDataInterface) {
            $this->value[] = $item;
            return true;
        }
        return false;
    }
}
