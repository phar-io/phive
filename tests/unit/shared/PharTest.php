<?php
namespace PharIo\Phive {

    /**
     * @covers PharIo\Phive\Phar
     */
    class PharTest extends \PHPUnit_Framework_TestCase {

        use ScalarTestDataProvider;

        /**
         * @dataProvider stringProvider
         *
         * @param string $name
         */
        public function testGetName($name) {
            $phar = new Phar($name, new Version('1.0.0'), new File(new Filename('foo.phar'), ''));
            $this->assertSame($name, $phar->getName());
        }

        /**
         * @dataProvider versionProvider
         *
         * @param Version $version
         */
        public function testGetVersion(Version $version) {
            $phar = new Phar('foo', $version, new File(new Filename('bar.phar'), ''));
            $this->assertEquals($version, $phar->getVersion());
        }

        /**
         * @dataProvider fileProvider
         *
         * @param File $file
         */
        public function testGetFile(File $file) {
            $phar = new Phar('foo', new Version('1.0.0'), $file);
            $this->assertEquals($file, $phar->getFile());
        }

        /**
         * @return array
         */
        public function versionProvider() {
            return [
                [new Version('1.0.0')],
                [new Version('3.5.2')]
            ];
        }

        public function fileProvider() {
            return [
                [new File(new Filename('foo.phar'), 'bar')],
                [new File(new Filename('bar.phar'), 'baz')],
            ];
        }

    }

}


