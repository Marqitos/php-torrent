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

class BEList implements BEncodeDataInterface, Iterator, Countable {
    use IterableTrait;

# Constructor
    public function __construct(array $list) {
        $this->value = $list;
        $this->generator = $this->getGenerator();
    }
# -- Constructor

# Members of BEncodeTypeInterface
    public BEncodeType $type {
        get => BEncodeType::List;
    };
## -- Members of BEncodeTypeInterface

# Members of BEncodeDataInterface
    public array $value {
        get => $this->value;
        protected => $this->value = $value;
    }

    public static function decode(&$raw, &$offset): BEncodeTypeInterface {
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

            array_push($list, $value);
        }
        return new static($list);
    }

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
    public function __unserialize(array $data): void {
        $this->value = [];
        foreach ($data as $item) {
            $this->add($item);
        }
    }
# -- Members of Serializable

    protected function getGenerator(): Generator {
        foreach ($this->value as $value) {
            yield $value;
        }
    }

    public function add(BEncodeDataInterface|int|string|array|bool $item) {
        $item = BEncode::asBEncodeObject($item);
        if ($item instanceof BEncodeDataInterface) {
            $this->value[] = $item;
            return true;
        }
        return false;
    }
}
