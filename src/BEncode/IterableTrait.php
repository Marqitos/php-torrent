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

trait IterableTrait {
    // implements Iterator
    // implements Countable
    // implements Serializable
    // implements Stringable

    protected Generator $generator;
    abstract public array $value { get; }
    abstract protected function getGenerator(): Generator;
    abstract public function encode(): string;

# Members of Iterator
    public function current(): mixed {
        return $this->generator->current();
    }
    public function key(): mixed {
        return $this->generator->key();
    }
    public function next(): void {
        $this->generator->next();
    }
    public function rewind(): void {
        $this->generator->rewind();
    }
    public function valid(): bool {
        return $this->generator->valid();
    }
# -- Members of Iterator

# Members of Countable
public function count(): int {
    return $this->value;
}
# -- Members of Countable

# Members of Serializable
    public function serialize(): ?string {
        return serialize($this->value);
    }
    public function unserialize(string $data): void {
        $this->value = unserialize($data);
    }
    public function __serialize(): array {
        return $this->asDecoded($this->value);
    }
    abstract public function __unserialize(array $data): void;
    public function __wakeup(): void {
        $this->generator = $this->getGenerator();
    }
# -- Members of Serializable

# Members of Stringable
    public function __toString(): string {
        return $this->encode();
    }
# -- Members of Stringable

    public function asDecoded(array $values = null): array {
        if ($values === null) {
            $values = $this->value;
        }
        foreach ($values as &$item) {
            if ($item instanceof BEncodeDataInterface) {
                $item = $item->value;
                if (is_array($item)) {
                    $this->asDecoded($item);
                }
            }
        }
    }
}
