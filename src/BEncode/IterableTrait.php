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

use Iterator;

use function count;

trait IterableTrait {
    // implements Iterator
    // implements Countable
    // implements Serializable
    // implements Stringable

    /**
     * Iterable item
     *
     * @var Iterator
     */
    protected Iterator $iterator;
    /**
     * Gets the iterator of the value
     *
     * @return Iterator
     */
    abstract protected function getIterator(): Iterator;
    /**
     * Returns de BEncoded string that represent the data
     *
     * @return string BEncoded string
     */
    abstract public function encode(): string;

# Members of Iterator
    /**
     * Return the current iterator item
     *
     * @return mixed
     */
    public function current(): mixed {
        return $this->iterator->current();
    }
    /**
     * Return the current iterator key
     *
     * @return mixed
     */
    public function key(): mixed {
        return $this->iterator->key();
    }
    /**
     * Move the iterator to the next value
     *
     * @return void
     */
    public function next(): void {
        $this->iterator->next();
    }
    /**
     * Rewind the iterator to the first value
     *
     * @return void
     */
    public function rewind(): void {
        $this->iterator = $this->getIterator();
    }
    /**
     * Check if the current iterator position is valid
     *
     * @return bool
     */
    public function valid(): bool {
        return $this->iterator->valid();
    }
# -- Members of Iterator

# Members of Countable
    /**
     * Return the num of items in the list
     *
     * @return integer
     */
    public function count(): int {
        return count($this->value);
    }
# -- Members of Countable

# Members of Serializable
    /**
     * Return data as string
     *
     * @return string
     */
    public function serialize(): ?string {
        $values = $this->asDecoded($this->value);
        return serialize($values);
    }
    /**
     * Set value from string
     *
     * @param  string $data Serialized data
     * @return void
     */
    abstract public function unserialize(string $data): void;
    /**
     * Return data as array
     *
     * @return array
     */
    public function __serialize(): array {
        return $this->asDecoded($this->value);
    }
    /**
     * Set value from array
     *
     * @param  array $data Unserialized data
     * @return void
     */
    abstract public function __unserialize(array $data): void;
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

    /**
     * Return the list as primitive values, recursively
     *
     * @param  array|null $values (Optional) Values to be decoded, default is the current value
     * @return array                         Array of decoded values
     */
    public function asDecoded(?array $values = null): array {
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
        return $values;
    }
}
