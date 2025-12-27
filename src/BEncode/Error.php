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

class Error implements BEncodeTypeInterface {
# Constructor
    public function __construct(string $error) {
        $this->value = $error;
    }
# -- Constructor

# Members of BEncodeTypeInterface
    public BEncodeType $type {
        get => BEncodeType::Error;
    };
## -- Members of BEncodeTypeInterface

    public string $value {
        get => $this->value;
        protected set => $this->value = $value;
    }
}
