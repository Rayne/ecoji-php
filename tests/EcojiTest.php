<?php

/**
 * (c) Dennis Meckel
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace Rayne\Ecoji;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class EcojiTest extends TestCase
{
    /**
     * @var Ecoji
     */
    private $ecoji;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->ecoji = new Ecoji;
    }

    /**
     * @return array
     */
    public function provideEncoding(): array
    {
        return json_decode(file_get_contents(dirname(__DIR__) . '/assets/tests.json'), true);
    }

    /**
     * @dataProvider provideEncoding
     * @param string $input
     * @param string $expected
     */
    public function testEncoding(string $input, string $expected)
    {
        $this->assertSame($expected, $this->ecoji->encode($input), 'Input: ' . $input);
    }

    /**
     *
     */
    public function testEncodingAndDecodingOfUnicode()
    {
        $this->assertSame('🥴📉🤔🙋', $this->ecoji->encode('🍟'));
        $this->assertSame('🍟', $this->ecoji->decode('🥴📉🤔🙋'));
    }

    /**
     * @return array
     */
    public function provideDecoding(): array
    {
        return array_map(function ($map) {
            return [$map[1], $map[0]];
        }, $this->provideEncoding());
    }

    /**
     * @dataProvider provideDecoding
     * @param string $input
     * @param string $expected
     */
    public function testDecoding(string $input, string $expected)
    {
        $this->assertSame($expected, $this->ecoji->decode($input), 'Input: ' . $input);
    }

    /**
     * @return array
     */
    public function provideInvalidDecoding()
    {
        $fourEmojisExpected = 'Unexpected end of data. Expected blocks of four emojis.';

        return [
            ['🍟', $fourEmojisExpected],
            ['🍟🍟', $fourEmojisExpected],
            ['🍟🍟🍟', $fourEmojisExpected],

            ['🍟🍟🍟🍟🍟', $fourEmojisExpected],
            ['🍟🍟🍟🍟🍟🍟', $fourEmojisExpected],
            ['🍟🍟🍟🍟🍟🍟🍟', $fourEmojisExpected],

            ['→', $fourEmojisExpected],
            ['→↑', $fourEmojisExpected],
            ['→↑←', $fourEmojisExpected],
            ['→↑←↓', 'Invalid rune: →'],
        ];
    }

    /**
     * @dataProvider provideInvalidDecoding
     */
    public function testInvalidDecoding($input, $exceptionMessage)
    {
        try {
            $this->ecoji->decode($input);
        } catch (InvalidArgumentException $e) {
            $this->assertSame($exceptionMessage, $e->getMessage());
            return;
        }

        $this->fail();
    }
}
