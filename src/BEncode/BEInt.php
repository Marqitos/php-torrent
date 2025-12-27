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

use function filter_var;
use function strpos;
use function substr;

use const FILTER_VALIDATE_INT;

class BEInt implements BEncodeDataInterface {

    public function __construct(int $value) {
        $this->value = $value;
    }

# Members of BEncodeTypeInterface
    public BEncodeType $type {
        get => BEncodeType::Int;
    };
## -- Members of BEncodeTypeInterface

# Members of BEncodeDataInterface
    public int $value {
        get => $this->value;
        set => $this->value = $value;
    }

    public static function decode(&$raw, &$offset): BEncodeTypeInterface {
        $end = strpos($raw, "e", $offset);
        $value = filter_var(substr($raw, ++$offset, $end - $offset), FILTER_VALIDATE_INT);
        if ($value === false) {
            return new Error("Invalid integer value");
        }
        $offset += $end - $offset;
        return new static($value);
    }

    public function encode(): string {
        return "i" . $this->value . "e";
    }
# -- Members of BEncodeDataInterface

# Members of Serializable
    public function serialize(): ?string {
        return (string) $this->value;
    }
    public function unserialize(string $data): void {
        $this->value = (int) $data;
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
