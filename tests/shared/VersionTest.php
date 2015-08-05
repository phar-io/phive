<?php
namespace PharIo\Phive {

    class VersionTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider versionProvider
         *
         * @param string $versionString
         * @param string $expectedMajor
         * @param string $expectedMinor
         * @param string $expectedPatch
         * @param string $expectedLabel
         * @param string $expectedMetadata
         */
        public function testParsesVersionNumbers(
            $versionString, $expectedMajor, $expectedMinor, $expectedPatch, $expectedLabel = '', $expectedMetadata = ''
        ) {
            $version  = new Version($versionString);
            $this->assertSame($expectedMajor, $version->getMajor());
            $this->assertSame($expectedMinor, $version->getMinor());
            $this->assertSame($expectedPatch, $version->getPatch());
            $this->assertSame($expectedLabel, $version->getLabel());
            $this->assertSame($expectedMetadata, $version->getBuildMetadata());
            $this->assertSame($versionString, $version->getVersionString());
        }

        public function versionProvider() {
            return [
                ['0.0.1', '0', '0', '1'],
                ['0.1.2', '0', '1', '2'],
                ['1.0.0-alpha', '1', '0', '0', 'alpha'],
                ['0.0.1-dev+ABC', '0', '0', '1', 'dev', 'ABC'],
                ['1.0.0-x.7.z.92', '1', '0', '0', 'x.7.z.92']
            ];
        }

    }

}

