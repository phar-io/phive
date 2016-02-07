<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\Filename
 */
class FilenameTest extends \PHPUnit_Framework_TestCase {

    public function testCanBeConvertedToString() {
        $this->assertEquals(
            'abc',
            (string)(new Filename('abc'))
        );
    }

    public function testFileExistsReturnsFalseOnMissingFile() {
        $name = new Filename('/does/not/exist');
        $this->assertFalse($name->exists());
    }

    public function testFileExistsReturnsTrueOnExistingFile() {
        $name = new Filename(__FILE__);
        $this->assertTrue($name->exists());
    }

    public function testInvalidTypeForFilenameThrowsException() {
        $this->setExpectedException(\InvalidArgumentException::class);
        new Filename(new \StdClass);
    }

}
