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

use Exception;

class Error extends Exception implements BEncodeTypeInterface {
# Constructor
    /**
     * Creates a new instance or Error
     *
     * @param  string $message
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }
# -- Constructor

# Members of BEncodeTypeInterface
    /**
     * @inheritDoc
     */
    public BEncodeType $type {
        get => BEncodeType::Error;
    }
## -- Members of BEncodeTypeInterface
}
