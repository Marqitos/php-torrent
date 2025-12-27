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

class BEString implements BEncodeDataInterface {

    public function __construct(string $value) {
        $this->value = $value;
    }

# Members of BEncodeTypeInterface
    public BEncodeType $type {
        get => BEncodeType::String;
    };
## -- Members of BEncodeTypeInterface

# Members of BEncodeDataInterface
    public string $value {
        get => $this->value;
        set => $this->value = $value;
    }

    public static function decode(&$raw, &$offset): BEncodeTypeInterface {
        $end = strpos($raw, ":", $offset);
        if ($end === false) {
            throw new Error("Length not followed by :");
        }
        $len = substr($raw, $offset, $end - $offset);
        $offset += $len + $end - $offset;
        $end++;
        return new static(substr($raw, $end, $len));
    }

    public function encode(): string {
        $len = strlen($this->value);
        return (string) $len . ":" . $this->value;
    }
# -- Members of BEncodeDataInterface

# Members of Serializable
    public function serialize(): ?string {
        return $this->value;
    }
    public function unserialize(string $data): void {
        $this->value = $data;
    }
    public function __serialize(): array {
        return ['value' => $this->value];
    }
    public function __unserialize(array $data): void {
        $this->value = $data['value'];
    }
# -- Members of Serializable

# Members of Stringable
    public function __toString(): string {
        return $this->encode();
    }
# -- Members of Stringable
}
