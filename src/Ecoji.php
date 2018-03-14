<?php

/**
 * (c) Dennis Meckel
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace Rayne\Ecoji;

use InvalidArgumentException;
use RuntimeException;

/**
 *
 */
class Ecoji
{
    /**
     * @var EmojiMappingInterface
     */
    private $mapping;

    /**
     * @param EmojiMappingInterface|null $mapping
     */
    public function __construct(EmojiMappingInterface $mapping = null)
    {
        $this->mapping = $mapping ?: new EmojiMapping;
    }

    /**
     * @param string $input
     * @return string
     */
    public function encode(string $input): string
    {
        $textRemaining = $input;
        $output = '';

        while (strlen($textRemaining)) {
            $buffer = [];
            $textCurrent = substr($textRemaining, 0, 5);
            $textRemaining = substr($textRemaining, 5);

            for ($i = 0; $i < 5; $i++) {
                $buffer[$i] = strlen($textCurrent) > $i ? ord($textCurrent[$i]) : 0;
            }

            switch (strlen($textCurrent)) {
                case 1:
                    $output .= $this->mapping->getEmoji($buffer[0] << 2 | $buffer[1] >> 6);
                    $output .= $this->mapping->getPadding();
                    $output .= $this->mapping->getPadding();
                    $output .= $this->mapping->getPadding();
                    break;
                case 2:
                    $output .= $this->mapping->getEmoji($buffer[0] << 2 | $buffer[1] >> 6);
                    $output .= $this->mapping->getEmoji(($buffer[1] & 0x3f) << 4 | $buffer[2] >> 4);
                    $output .= $this->mapping->getPadding();
                    $output .= $this->mapping->getPadding();
                    break;
                case 3:
                    $output .= $this->mapping->getEmoji($buffer[0] << 2 | $buffer[1] >> 6);
                    $output .= $this->mapping->getEmoji(($buffer[1] & 0x3f) << 4 | $buffer[2] >> 4);
                    $output .= $this->mapping->getEmoji(($buffer[2] & 0x0f) << 6 | $buffer[3] >> 2);
                    $output .= $this->mapping->getPadding();
                    break;
                case 4:
                    $output .= $this->mapping->getEmoji($buffer[0] << 2 | $buffer[1] >> 6);
                    $output .= $this->mapping->getEmoji(($buffer[1] & 0x3f) << 4 | $buffer[2] >> 4);
                    $output .= $this->mapping->getEmoji(($buffer[2] & 0x0f) << 6 | $buffer[3] >> 2);

                    // Look at the last two bits to determine the padding.
                    switch ($buffer[3] & 0x03) {
                        case 0:
                            $output .= $this->mapping->getPadding40();
                            break;
                        case 1:
                            $output .= $this->mapping->getPadding41();
                            break;
                        case 2:
                            $output .= $this->mapping->getPadding42();
                            break;
                        case 3:
                            $output .= $this->mapping->getPadding43();
                            break;
                    }
                    break;
                case 5:
                    // use 8 bits from 1st byte and 2 bits from 2nd byte to lookup emoji
                    $output .= $this->mapping->getEmoji($buffer[0] << 2 | $buffer[1] >> 6);

                    // use 6 bits from 2nd byte and 4 bits from 3rd byte to lookup emoji
                    $output .= $this->mapping->getEmoji(($buffer[1] & 0x3f) << 4 | $buffer[2] >> 4);

                    // use 4 bits from 3rd byte and 6 bits from 4th byte to lookup emoji
                    $output .= $this->mapping->getEmoji(($buffer[2] & 0x0f) << 6 | $buffer[3] >> 2);

                    //user 2 bits from 4th byte and 8 bits from 5th byte to lookup emoji
                    $output .= $this->mapping->getEmoji(($buffer[3] & 0x03) << 8 | $buffer[4]);
                    break;
            }
        }

        return $output;
    }

    /**
     * @param string $input Ecoji encoded information.
     * @return string
     */
    public function decode(string $input): string
    {
        $textRemaining = str_replace("\n", '', $input);
        $result = '';

        while (strlen($textRemaining)) {
            if (mb_strlen($textRemaining) < 4) {
                throw new InvalidArgumentException('Unexpected end of data. Expected blocks of four emojis.');
            }

            $runes = [
                mb_substr($textRemaining, 0, 1),
                mb_substr($textRemaining, 1, 1),
                mb_substr($textRemaining, 2, 1),
                mb_substr($textRemaining, 3, 1),
            ];

            $textRemaining = mb_substr($textRemaining, 4);

            $bits1 = $this->mapping->getId($runes[0]);
            $bits2 = $this->mapping->getId($runes[1]);
            $bits3 = $this->mapping->getId($runes[2]);
            $bits4 = null;

            switch ($runes[3]) {
                case $this->mapping->getPadding40():
                    $bits4 = 0;
                    break;
                case $this->mapping->getPadding41():
                    $bits4 = 1 << 8;
                    break;
                case $this->mapping->getPadding42():
                    $bits4 = 2 << 8;
                    break;
                case $this->mapping->getPadding43():
                    $bits4 = 3 << 8;
                    break;
                default:
                    $bits4 = $this->mapping->getId($runes[3]);
            }

            $out = [
                $bits1 >> 2,
                (($bits1 & 0x3) << 6) | ($bits2 >> 4),
                (($bits2 & 0xf) << 4) | ($bits3 >> 6),
                (($bits3 & 0x3f) << 2) | ($bits4 >> 8),
                $bits4 & 0xff,
            ];

            if ($runes[1] == $this->mapping->getPadding()) {
                $out = array_slice($out, 0, 1);
            } elseif ($runes[2] == $this->mapping->getPadding()) {
                $out = array_slice($out, 0, 2);
            } elseif ($runes[3] == $this->mapping->getPadding()) {
                $out = array_slice($out, 0, 3);
            } elseif ($runes[3] == $this->mapping->getPadding40() || $runes[3] == $this->mapping->getPadding41() || $runes[3] == $this->mapping->getPadding42() || $runes[3] == $this->mapping->getPadding43()) {
                $out = array_slice($out, 0, 4);
            }

            foreach ($out as $v) {
                $result .= chr($v);
            }
        }

        return $result;
    }
}
