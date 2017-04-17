<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\File
 */
class FileTest extends TestCase {

    /**
     * @dataProvider sha1HashProvider
     *
     * @param string $content
     * @param string $expectedHash
     */
    public function testGeneratesExpectedSha1Hash($content, $expectedHash) {
        $expectedHash = new Sha1Hash($expectedHash);
        $file = new File(new Filename('foo.phar'), $content);
        $this->assertEquals($expectedHash, $file->getSha1Hash());
    }

    public function sha1HashProvider() {
        return [
            ['some content', '94e66df8cd09d410c62d9e0dc59d3a884e458e05'],
            ['some other content', '2c467095b0a0e67be51f6bd00f80cb2118846ddc']
        ];
    }

    /**
     * @dataProvider sha256HashProvider
     *
     * @param string $content
     * @param string $expectedHash
     */
    public function testGeneratesExpectedSha256Hash($content, $expectedHash) {
        $expectedHash = new Sha256Hash($expectedHash);
        $file = new File(new Filename('foo.phar'), $content);
        $this->assertEquals($expectedHash, $file->getSha256Hash());
    }

    public function sha256HashProvider() {
        return [
            ['some content', '290f493c44f5d63d06b374d0a5abd292fae38b92cab2fae5efefe1b0e9347f56'],
            ['some other content', 'f73f16ede021d01efecf627b5e658be52293f167cfe06c6b8d0e591cb25b68c9']
        ];
    }

    public function testFilename() {
        $filename = new Filename('foo.phar');
        $file = new File($filename, 'bar');
        $this->assertSame($filename, $file->getFilename());
    }

    public function testContent() {
        $file = new File(new Filename('foo.phar'), 'bar');
        $this->assertSame('bar', $file->getContent());
    }

    /**
     * @uses \PharIo\Phive\Filename
     */
    public function testSaveAs() {
        $target = sys_get_temp_dir() . '/testfile';
        $file = new File(new Filename('foo.phar'), 'bar');
        $file->saveAs(new Filename($target));

        $this->assertFileExists($target);
        $this->assertSame('bar', file_get_contents($target));
        unlink($target);
    }
}



