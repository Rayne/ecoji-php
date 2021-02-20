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
use PHPUnit\Framework\TestCase;

class EcojiStreamTest extends TestCase
{
    /**
     * @var resource
     */
    private $source;

    /**
     * @var resource
     */
    private $destination;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->source = fopen('php://temp', 'rw');
        $this->destination = fopen('php://temp', 'rw');
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        fclose($this->source);
        fclose($this->destination);
    }

    /**
     * @dataProvider \Rayne\Ecoji\EcojiTest::provideEncoding
     * @param string $input
     * @param string $expected
     */
    public function testEncoding(string $input, string $expected)
    {
        fwrite($this->source, $input);
        rewind($this->source);

        (new EcojiStream)->encode($this->source, $this->destination);

        rewind($this->destination);

        $this->assertSame($expected, stream_get_contents($this->destination), 'Input: ' . $input);
    }

    /**
     * @dataProvider \Rayne\Ecoji\EcojiTest::provideDecoding
     * @param string $input
     * @param string $expected
     */
    public function testDecoding(string $input, string $expected)
    {
        fwrite($this->source, $input);
        rewind($this->source);

        (new EcojiStream)->decode($this->source, $this->destination);

        rewind($this->destination);

        $this->assertSame($expected, stream_get_contents($this->destination), 'Input: ' . $input);
    }

    /**
     * @return array
     */
    public function provideDecodingOfInvalidMessage(): array
    {
        return [
            [chr(0xFF)],
            [str_repeat(chr(0xFF), 2)],
            [str_repeat(chr(0xFF), 3)],
            [str_repeat(chr(0xFF), 4)],
            [str_repeat(chr(0xFF), 8)],
        ];
    }

    /**
     * @dataProvider provideDecodingOfInvalidMessage
     * @param string $invalidMessage
     */
    public function testDecodingOfInvalidMessage(string $invalidMessage)
    {
        $this->expectException(InvalidArgumentException::class);

        fwrite($this->source, $invalidMessage);
        rewind($this->source);

        (new EcojiStream)->decode($this->source, $this->destination);
    }

    /**
     *
     */
    public function testDecodingOfIncompleteMessage()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Ecoji encoding: 🍉');

        // Five UTF-8 characters but valid Ecoji encoded messages have a multiple of four characters.
        fwrite($this->source, '🏯🔩🚗🌷🍉');
        rewind($this->source);

        (new EcojiStream)->decode($this->source, $this->destination);
    }

    /**
     *
     */
    public function testInvalidWrap()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('-1');

        (new EcojiStream)->setWrap(-1);
    }

    /**
     * @return array
     */
    public function provideWrappedEncoding(): array
    {
        return [
            ['Hello World', 1, "🏯\n🔩\n🚗\n🌷\n🍉\n👇\n🦒\n🕊\n👡\n☕\n☕\n☕"],
            ['Hello World', 2, "🏯🔩\n🚗🌷\n🍉👇\n🦒🕊\n👡☕\n☕☕"],
            ['Hello World', 3, "🏯🔩🚗\n🌷🍉👇\n🦒🕊👡\n☕☕☕"],
            ['Hello World', 10, "🏯🔩🚗🌷🍉👇🦒🕊👡☕\n☕☕"],
            ['Hello World', 12, "🏯🔩🚗🌷🍉👇🦒🕊👡☕☕☕"],
            ['Hello World', 42, "🏯🔩🚗🌷🍉👇🦒🕊👡☕☕☕"],
        ];
    }

    /**
     * @dataProvider provideWrappedEncoding
     * @param string $input
     * @param int $wrap
     * @param $expected
     */
    public function testWrappedEncoding(string $input, int $wrap, $expected)
    {
        fwrite($this->source, $input);
        rewind($this->source);

        (new EcojiStream)
            ->setWrap($wrap)
            ->encode($this->source, $this->destination);

        rewind($this->destination);

        $this->assertSame($expected, stream_get_contents($this->destination), 'Input: ' . $input);
    }

    /**
     *
     */
    public function testWrappedDecoding()
    {
        fwrite($this->source, "\n🏯\n🔩\n🚗\n🌷\n🍉\n👇\n🦒\n🕊\n👡\n☕\n☕\n☕\n");
        rewind($this->source);

        (new EcojiStream)->decode($this->source, $this->destination);

        rewind($this->destination);

        $this->assertSame('Hello World', stream_get_contents($this->destination));
    }
}
