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

/**
 * Represents a BEncoded string
 */
class BEString implements BEncodeDataInterface {

    /**
     * Create a new instance of BEString
     *
     * @param  string $value String data
     */
    public function __construct(string $value) {
        $this->value = $value;
    }

# Members of BEncodeTypeInterface
    /**
     * @inheritDoc
     */
    public BEncodeType $type {
        get => BEncodeType::String;
    }
## -- Members of BEncodeTypeInterface

# Members of BEncodeDataInterface
    /**
     * @inheritDoc
     */
    public string $value {
        get => $this->value;
        set => $this->value = $value;
    }
    /**
     * @inheritDoc
     */
    public static function decode(string &$raw, int &$offset = 0): BEncodeTypeInterface {
        $end = strpos($raw, ":", $offset);
        if ($end === false) {
            throw new Error("Length not followed by :");
        }
        $len = filter_var(substr($raw, $offset, $end - $offset), FILTER_VALIDATE_INT);
        if ($len === false) {
            throw new Error("Invalid length");
        }
        $offset += $len + $end - $offset;
        $end++;
        return new static(substr($raw, $end, $len));
    }
    /**
     * @inheritDoc
     */
    public function encode(): string {
        $len = strlen($this->value);
        return (string) $len . ":" . $this->value;
    }
# -- Members of BEncodeDataInterface

# Members of Serializable
    /**
     * Return data as string
     *
     * @return string
     */
    public function serialize(): ?string {
        return $this->value;
    }
    /**
     * Set value from string
     *
     * @param  string $data Serialized data
     * @return void
     */
    public function unserialize(string $data): void {
        $this->value = $data;
    }
    /**
     * Return data as array
     *
     * @return array
     */
    public function __serialize(): array {
        return [$this->value];
    }
    /**
     * Set value from array
     *
     * @param  array $data Unserialized data
     * @return void
     */
    public function __unserialize(array $data): void {
        $this->value = $data[0];
    }
# -- Members of Serializable

# Members of Stringable
    /**
     * Return data as bencoded string
     *
     * @return string
     */
    public function __toString(): string {
        return $this->encode();
    }
# -- Members of Stringable
}
