<?php
namespace TheSeer\Phive {

    class PharFileTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider sha1HashProvider
         *
         * @param string $content
         * @param string $expectedHash
         */
        public function testGeneratesExpectedSha1Hash($content, $expectedHash) {
            $file = new PharFile('foo.phar', $content);
            $this->assertSame($expectedHash, $file->getSha1Hash());
        }

        public function sha1HashProvider() {
            return [
                ['some content', '94e66df8cd09d410c62d9e0dc59d3a884e458e05'],
                ['some other content', '2c467095b0a0e67be51f6bd00f80cb2118846ddc']
            ];
        }

        public function testFilename() {
            $file = new PharFile('foo.phar', 'bar');
            $this->assertSame('foo.phar', $file->getFilename());
        }

        public function testContent() {
            $file = new PharFile('foo.phar', 'bar');
            $this->assertSame('bar', $file->getContent());
        }

    }

}

