<?php

/**
 * (c) Dennis Meckel
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace Rayne\Ecoji;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/**
 * @see EcojiTest
 */
class EmojiMappingTest extends TestCase
{
    /**
     * @return array
     */
    public function provideInvalidGetEmojiCall(): array
    {
        return [
            [-1],
            [1024],
        ];
    }

    /**
     * @dataProvider provideInvalidGetEmojiCall
     * @param int $id
     */
    public function testInvalidGetEmojiCall(int $id)
    {
        try {
            (new EmojiMapping)->getEmoji($id);
        } catch (OutOfBoundsException $e) {
            $this->assertSame('Invalid ID: ' . $id, $e->getMessage());
            return;
        }

        $this->fail();
    }
}
