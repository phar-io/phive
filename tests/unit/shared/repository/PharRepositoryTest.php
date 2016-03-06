<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\PharRepository
 */
class PharRepositoryTest extends \PHPUnit_Framework_TestCase {

    public function testReturnsExpectedUnusedPhars() {
        $repo = new PhiveInstallDB(new Filename(__DIR__ . '/fixtures/phars.xml'), new Directory(__DIR__ . '/fixtures'));

        $expected = [
            new Phar('phpunit', new Version('4.8.7'), new File(new Filename('phpunit-4.8.7.phar.dummy'), 'phpunit-4.8.7')),
            new Phar('phpunit', new Version('4.8.6'), new File(new Filename('phpunit-4.8.6.phar.dummy'), 'phpunit-4.8.6')),
        ];
        $actual = $repo->getUnusedPhars();

        $this->assertEquals($expected, $actual);
    }

    protected function setUp() {
        TestStreamWrapper::register('test', __DIR__ . '/fixtures/');
    }

    protected function tearDown() {
        TestStreamWrapper::unregister();
    }

}




