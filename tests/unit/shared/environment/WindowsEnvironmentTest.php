<?php
namespace PharIo\Phive;

/**
 * @covers \PharIo\Phive\WindowsEnvironment
 */
class WindowsEnvironmentTest extends \PHPUnit_Framework_TestCase {


    public function testSupportsColoredOutputReturnsFalse() {
        $environment = new WindowsEnvironment([]);
        $this->assertFalse($environment->supportsColoredOutput());
    }

    public function testSupportsColoredOutputReturnsTrueIfAnsiconIsUsed() {
        $environment = new WindowsEnvironment(['ANSICON' => true]);
        $this->assertTrue($environment->supportsColoredOutput());
    }

    public function testSupportsColoredOutputReturnsTrueIfConEmuAnsiIsUsed() {
        $environment = new WindowsEnvironment(['ConEmuANSI' => true]);
        $this->assertTrue($environment->supportsColoredOutput());
    }

}
