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
 * Mapping implementation with the default emoji set defined by the "Ecoji/Encoji Standard".
 *
 * @see https://github.com/keith-turner/ecoji
 */
class EmojiMapping implements EmojiMappingInterface
{
    /**
     * @var string[]
     */
    private $emojis;

    /**
     * @var string[]
     */
    private $revEmojis;

    /**
     * @var string
     */
    private $padding = '☕';

    /**
     * @var string
     */
    private $padding40 = '⚜';

    /**
     * @var string
     */
    private $padding41 = '🏍';

    /**
     * @var string
     */
    private $padding42 = '📑';

    /**
     * @var string
     */
    private $padding43 = '🙋';

    /**
     *
     */
    public function __construct()
    {
        $this->emojis = require dirname(__DIR__) . '/assets/emojis.php';

        $this->revEmojis = array_flip($this->emojis);
        $this->revEmojis[$this->padding] = 0;
    }

    /**
     * @inheritdoc
     */
    public function getEmoji(int $id): string
    {
        if (!isset($this->emojis[$id])) {
            throw new OutOfBoundsException('Invalid ID: ' . $id);
        }

        return $this->emojis[$id];
    }

    /**
     * @inheritdoc
     */
    public function getId(string $emoji): int
    {
        if (!isset($this->revEmojis[$emoji])) {
            throw new InvalidArgumentException('Invalid rune: ' . $emoji);
        }

        return $this->revEmojis[$emoji];
    }

    /**
     * @inheritdoc
     */
    public function getPadding(): string
    {
        return $this->padding;
    }

    /**
     * @inheritdoc
     */
    public function getPadding40(): string
    {
        return $this->padding40;
    }

    /**
     * @inheritdoc
     */
    public function getPadding41(): string
    {
        return $this->padding41;
    }

    /**
     * @inheritdoc
     */
    public function getPadding42(): string
    {
        return $this->padding42;
    }

    /**
     * @inheritdoc
     */
    public function getPadding43(): string
    {
        return $this->padding43;
    }
}
