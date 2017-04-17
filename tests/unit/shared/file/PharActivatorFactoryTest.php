<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\PharActivatorFactory
 */
class PharActivatorFactoryTest extends TestCase {

    public function testGetBatPharActivator() {
        $factory = new PharActivatorFactory();
        $this->assertInstanceOf(BatPharActivator::class, $factory->getBatPharActivator());
    }

    public function testGetSymlinkPharActivator() {
        $factory = new PharActivatorFactory();
        $this->assertInstanceOf(SymlinkPharActivator::class, $factory->getSymlinkPharActivator());
    }

}
