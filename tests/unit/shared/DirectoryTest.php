<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\Directory
 * @uses PharIo\Phive\DirectoryException
 */
class DirectoryTest extends \PHPUnit_Framework_TestCase {

    private $testDir;

    protected function setUp() {
        $this->testDir = __DIR__ . '/../../data/directory';
    }

    public function testCanBeConvertedToString() {
        $this->assertEquals($this->testDir, (string)(new Directory($this->testDir)));
    }

    public function testDirectoryIsCreatedWhenMissing() {
        $path = sys_get_temp_dir() . '/test';
        (new Directory($path, 0770));
        $this->assertFileExists($path);
        $this->assertEquals('0770', substr(sprintf('%o', fileperms($path)), -4));
        rmdir($path);
    }

    public function testTryingToInstantiateOnFileThrowsException() {
        $this->expectException(DirectoryException::class);
        $this->expectExceptionCode(DirectoryException::InvalidType);
        (new Directory($this->testDir . '/file'));
    }

    /**
     * @uses Filename
     */
    public function testRequestingFileFromDirectoryReturnsFilenameInstance() {
        $this->assertInstanceOf(
            Filename::class,
            (new Directory($this->testDir))->file('file')
        );
    }

    public function testRequestingChildFromDirectoryReturnsNewDirectoryInstance() {
        $child = (new Directory($this->testDir))->child('child');
        $this->assertInstanceOf(
            Directory::class,
            $child
        );
        $this->assertEquals('child', basename((string)$child));
    }

    public function testThrowsExceptionOnNonIntegerMode() {
        $this->expectException(DirectoryException::class);
        $this->expectExceptionCode(DirectoryException::InvalidMode);
        (new Directory('/', 'abc'));
        restore_error_handler();
    }

    public function testThrowsExceptionIfGivenPathCannotBeCreated() {
        $this->expectException(DirectoryException::class);
        $this->expectExceptionCode(DirectoryException::CreateFailed);
        set_error_handler(function() { throw new \ErrorException('caught'); });
        (new Directory('/arbitrary/non/exisiting/path', 0777));
        restore_error_handler();
    }

    /**
     * @dataProvider relativePathTestDataProvider
     *
     * @param $directory
     * @param $otherDirectory
     * @param $expected
     */
    public function testReturnsExpectedRelativePath($directory, $otherDirectory, $expected) {
        $directory = new Directory($directory);
        $otherDirectory = new Directory($otherDirectory);

        $this->assertEquals($expected, $directory->getRelativePathTo($otherDirectory));
    }

    public static function relativePathTestDataProvider() {
        return [
            [__DIR__ . '/../../data/directory', __DIR__ . '/../../data', './directory/'],
            [__DIR__ . '/../../data', __DIR__ . '/../../data/directory', '../']
        ];
    }
}
