<?php
namespace PharIo\Phive;

/**
 * @covers \PharIo\Phive\ETag
 */
class ETagTest extends \PHPUnit_Framework_TestCase {

    public function testCanBeConvertedToString() {
        $this->assertEquals(
            'abc',
            (new ETag('abc'))->asString()
        );
    }
}
