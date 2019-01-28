<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ETag
 */
class ETagTest extends TestCase {
    public function testCanBeConvertedToString(): void {
        $this->assertEquals(
            'abc',
            (new ETag('abc'))->asString()
        );
    }
}
