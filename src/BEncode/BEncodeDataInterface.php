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

use Serializable;
use Stringable;

interface BEncodeDataInterface extends BEncodeTypeInterface, Serializable, Stringable {
    public $value { get; };
    public static function decode(string &$raw, int &$offset = 0): BEncodeTypeInterface;
    public function encode(): string;
}
