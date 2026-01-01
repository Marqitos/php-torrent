<?php
/**
 * This file is part of the Rodas\Torrent library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Rodas\Torrent
 * @copyright 2026 Marcos Porto <php@marcospor.to>
 * @license https://opensource.org/license/mit The MIT License
 * @link https://marcospor.to/repositories/torrent
 */

declare(strict_types=1);

namespace Rodas\Torrent\BEncode;

use Serializable;
use Stringable;

interface BEncodeDataInterface extends BEncodeTypeInterface, Serializable, Stringable {
    /**
     * Gets de BEncoded value
     *
     * @var mixed
     */
    public mixed $value { get; }
    /**
     * Create a instance from a BEncoded string
     *
     * @param  string               $raw    BEncoded string, by ref
     * @param  integer              $offset Initial offset, by ref
     * @return BEncodeTypeInterface New object
     */
    public static function decode(string &$raw, int &$offset = 0): BEncodeTypeInterface;
    /**
     * Returns de BEncoded string that represent the data
     *
     * @return string BEncoded string
     */
    public function encode(): string;
}
