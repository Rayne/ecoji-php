<?php

/**
 * (c) Dennis Meckel
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace Rayne\Ecoji;

use InvalidArgumentException;
use OutOfBoundsException;

/**
 * Interface for the replaceable emoji mapping.
 */
interface EmojiMappingInterface
{
    /**
     * Convert ID to emoji.
     *
     * @param int $id
     * @return string
     * @throws OutOfBoundsException
     */
    public function getEmoji(int $id): string;

    /**
     * Convert emoji back to ID.
     *
     * @param string $emoji
     * @return int
     * @throws InvalidArgumentException
     */
    public function getId(string $emoji): int;

    /**
     * @return string
     */
    public function getPadding(): string;

    /**
     * @return string Padding emoji for the fourth byte with the least significant bits `00`.
     */
    public function getPadding40(): string;

    /**
     * @return string Padding emoji for the fourth byte with the least significant bits `01`.
     */
    public function getPadding41(): string;

    /**
     * @return string Padding emoji for the fourth byte with the least significant bits `10`.
     */
    public function getPadding42(): string;

    /**
     * @return string Padding emoji for the fourth byte with the least significant bits `11`.
     */
    public function getPadding43(): string;
}
