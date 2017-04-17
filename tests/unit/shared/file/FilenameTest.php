<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Filename
 */
class FilenameTest extends TestCase {

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
        $this->expectException(\InvalidArgumentException::class);
        new Filename(new \stdClass);
    }

    public function testReadThrowsExceptionIfFileDoesNotExist() {
        $name = new Filename('/does/not/exist');
        $this->expectException(\RuntimeException::class);
        $name->read();
    }

    public function testReadReturnsExpectedFile() {
        $name = new Filename(__DIR__ . '/../fixtures/file.txt');
        $expectedFile = new File($name, 'foo');
        $this->assertEquals($expectedFile, $name->read());
    }

}
