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
 * TODO A stream filter implementation (`php_user_filter`) would be nice. But how would the filter be configured?
 */
class EcojiStream
{
    /**
     * @var Ecoji
     */
    private $ecoji;

    /**
     * @var int
     */
    private $wrap = 0;

    /**
     * @param Ecoji|null $ecoji
     */
    public function __construct(Ecoji $ecoji = null)
    {
        $this->ecoji = $ecoji ?: new Ecoji;
    }

    /**
     * Wrapping is disabled by default.
     *
     * @param int $wrap `0` disables wrapping.
     * @return $this
     * @throws OutOfBoundsException
     */
    public function setWrap(int $wrap)
    {
        if ($wrap < 0) {
            throw new OutOfBoundsException($wrap);
        }

        $this->wrap = $wrap;

        return $this;
    }

    /**
     * Encodes the source stream. The destination stream will not receive a trailing newline.
     *
     * @param resource $source
     * @param resource $destination
     */
    public function encode($source, $destination)
    {
        $printNewline = false;
        $untilWrap = $this->wrap;
        $wrap = $this->wrap;

        // Read multiples of five bytes at a time to prevent padding when encoding.
        // Padding is only allowed for the last four or less bytes.
        while ('' !== ($bytes = stream_get_contents($source, 5))) {
            if ($wrap) {
                $buffer = $this->ecoji->encode($bytes); // Up to five bytes get encoded …
                $bufferLength = mb_strlen($buffer); // … and exactly four emojis are returned.

                while ($bufferLength > 0) {
                    if ($printNewline) {
                        $printNewline = false;
                        fwrite($destination, "\n");
                    }

                    if ($bufferLength < $untilWrap) {
                        fwrite($destination, $buffer);
                        $untilWrap -= $bufferLength;
                        break;
                    }

                    $printNewline = true;

                    if ($bufferLength == $untilWrap) {
                        fwrite($destination, $buffer);
                        $untilWrap = $wrap;
                        break;
                    }

                    fwrite($destination, mb_substr($buffer, 0, $untilWrap));
                    $buffer = mb_substr($buffer, $untilWrap);
                    $bufferLength -= $untilWrap;
                    $untilWrap = $wrap;
                }

                continue;
            }

            fwrite($destination, $this->ecoji->encode($bytes));
        }
    }

    /**
     * @param resource $source
     * @param resource $destination
     */
    public function decode($source, $destination)
    {
        $chars = [];

        while ('' !== ($char = $this->readUnicodeChar($source))) {
            // Skip newlines as Ecoji ignores them when decoding.
            // To be more precise: `decode()` throws newlines away
            // and expects groups of four remaining UTF-8 characters.
            if ($char === "\n") {
                continue;
            }

            $chars[] = $char;

            if (count($chars) == 4) {
                fwrite($destination, $this->ecoji->decode(implode('', $chars)));
                $chars = [];
            }
        }

        // Un-decoded characters remaining => Invalid Ecoji encoding!
        if (count($chars) !== 0) {
            throw new InvalidArgumentException('Invalid Ecoji encoding: ' . implode('', $chars));
        }
    }

    /**
     * @param resource $stream
     * @return string
     */
    private function readUnicodeChar($stream): string
    {
        $bytes = '';

        // Read up to four bytes and return when a valid UTF-8 sequence was detected.
        for ($i = 0; $i < 4; $i++) {
            $bytes .= stream_get_contents($stream, 1);

            // EOF reached.
            if ($bytes === '') {
                return '';
            }

            if (mb_check_encoding($bytes, 'UTF-8')) {
                return $bytes;
            }
        }

        throw new InvalidArgumentException('Invalid Unicode: ' . $bytes);
    }

    //
    // TODO Compare the performance of the commented code below with the "simple" implementation above.
    //
    //private function readUnicodeChar($stream): string {
    //    $byte0 = stream_get_contents($stream, 1);
    //
    //    // EOF
    //    if ($byte0 === '') {
    //        return '';
    //    }
    //
    //    // 0xxxxxxx
    //    if (ord($byte0) < 0x80) {
    //        return $byte0;
    //    }
    //
    //    // 110yyyyy 10xxxxxx
    //    if ((ord($byte0) & 0xE0) === 0xC0) {
    //        $byte1 = stream_get_contents($stream, 1);
    //
    //        if ((ord($byte1) & 0xC0) !== 0x80) {
    //            throw new InvalidArgumentException('Invalid encoding.');
    //        }
    //
    //        return $byte0 . $byte1;
    //    }
    //
    //    // 1110zzzz 10yyyyyy 10xxxxxx
    //    if ((ord($byte0) & 0xF0) === 0xE0) {
    //        $byte1 = stream_get_contents($stream, 1);
    //
    //        if ((ord($byte1) & 0xC0) !== 0x80) {
    //            throw new InvalidArgumentException('Invalid encoding.');
    //        }
    //
    //        $byte2 = stream_get_contents($stream, 1);
    //
    //        if ((ord($byte2) & 0xC0) !== 0x80) {
    //            throw new InvalidArgumentException('Invalid encoding.');
    //        }
    //
    //        return $byte0 . $byte1 . $byte2;
    //    }
    //
    //    // 11110uuu 10uuzzzz 10yyyyyy 10xxxxxx
    //    if ((ord($byte0) & 0xF8) === 0xF0) {
    //        $byte1 = stream_get_contents($stream, 1);
    //
    //        if ((ord($byte1) & 0xC0) !== 0x80) {
    //            throw new InvalidArgumentException('Invalid encoding.');
    //        }
    //
    //        $byte2 = stream_get_contents($stream, 1);
    //
    //        if ((ord($byte2) & 0xC0) !== 0x80) {
    //            throw new InvalidArgumentException('Invalid encoding.');
    //        }
    //
    //        $byte3 = stream_get_contents($stream, 1);
    //
    //        if ((ord($byte3) & 0xC0) !== 0x80) {
    //            throw new InvalidArgumentException('Invalid encoding.');
    //        }
    //
    //        return $byte0 . $byte1 . $byte2 . $byte3;
    //    }
    //
    //    throw new InvalidArgumentException('Invalid encoding.');
    //}
}
