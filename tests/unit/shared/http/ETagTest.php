<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ETag
 */
class ETagTest extends TestCase {

    public function testCanBeConvertedToString() {
        $this->assertEquals(
            'abc',
            (new ETag('abc'))->asString()
        );
    }
}
