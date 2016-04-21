<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\PharRegistry
 */
class PharRegistryTest extends \PHPUnit_Framework_TestCase {

    public function testReturnsExpectedUnusedPhars() {
        $repo = new PharRegistry(
            new XmlFile(
                new Filename(__DIR__ . '/fixtures/phars.xml'),
                'https://phar.io/phive/installdb',
                'phars'
            ),
            new Directory(__DIR__ . '/fixtures')
        );

        $expected = [
            new Phar('phpunit', new Version('4.8.7'), new File(new Filename('phpunit-4.8.7.phar.dummy'), 'phpunit-4.8.7')),
            new Phar('phpunit', new Version('4.8.6'), new File(new Filename('phpunit-4.8.6.phar.dummy'), 'phpunit-4.8.6')),
        ];
        $actual = $repo->getUnusedPhars();

        $this->assertEquals($expected, $actual);
    }
    
    public function testReturnsExpectedUsedPharsByDestination() {
        $repo = new PharRegistry(
            new XmlFile(
                new Filename(__DIR__ . '/fixtures/phars.xml'),
                'https://phar.io/phive/installdb',
                'phars'
            ),
            new Directory(__DIR__ . '/fixtures')
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject|Directory $destination */
        $destination = $this->getMockWithoutInvokingTheOriginalConstructor(Directory::class);
        $destination->method('__toString')
            ->willReturn('/vagrant/phive/tools');

        $expected = [
            new Phar('phpab', new Version('1.20.0'), new File(new Filename('phpab-1.20.0.phar.dummy'), 'phpab-1.20.0')),
            new Phar('phpunit', new Version('5.2.10'), new File(new Filename('phpunit-5.2.10.phar.dummy'), 'phpunit-5.2.10')),
        ];
        $actual = $repo->getUsedPharsByDestination($destination);

        $this->assertEquals($expected, $actual);
    }

    public function testReturnsExpectedFingerprints() {
        $repo = new PharRegistry(
            new XmlFile(
                new Filename(__DIR__ . '/fixtures/phars.xml'),
                'https://phar.io/phive/installdb',
                'phars'
            ),
            new Directory(__DIR__ . '/fixtures')
        );

        $expected = [
            'E8406D0D82947747293778314AA394086372C20A',
            'D8406D0D82947747293778314AA394086372C20A'
        ];
        $actual = $repo->getKnownSignatureFingerprints('phpunit');

        $this->assertEquals($expected, $actual);
    }
    protected function setUp() {
        TestStreamWrapper::register('test', __DIR__ . '/fixtures/');
    }

    protected function tearDown() {
        TestStreamWrapper::unregister();
    }

}




