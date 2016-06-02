<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\PharActivatorFactory
 */
class PharActivatorFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testGetBatPharActivator() {
        $factory = new PharActivatorFactory();
        $this->assertInstanceOf(BatPharActivator::class, $factory->getBatPharActivator());
    }

    public function testGetSymlinkPharActivator() {
        $factory = new PharActivatorFactory();
        $this->assertInstanceOf(SymlinkPharActivator::class, $factory->getSymlinkPharActivator());
    }

}
