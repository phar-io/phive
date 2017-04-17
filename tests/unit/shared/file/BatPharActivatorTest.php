<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\BatPharActivator
 */
class BatPharActivatorTest extends TestCase {

    public function testCreatesExpectedBatFile() {
        $activator = new BatPharActivator('foo ##PHAR_FILENAME## bar');
        $createdFile = $activator->activate(new Filename('some.phar'), new Filename(__DIR__ . '/some'));

        $this->assertEquals('foo some.phar bar', file_get_contents($createdFile));
    }

    protected function tearDown() {
        if (file_exists(__DIR__ . '/some.bat')) {
            unlink(__DIR__ . '/some.bat');
        }
    }

}
