<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\WindowsEnvironment
 */
class WindowsEnvironmentTest extends TestCase {
    public function testSupportsColoredOutputReturnsFalse(): void {
        $environment = new WindowsEnvironment([]);
        $this->assertFalse($environment->supportsColoredOutput());
    }

    public function testSupportsColoredOutputReturnsTrueIfAnsiconIsUsed(): void {
        $environment = new WindowsEnvironment(['ANSICON' => true]);
        $this->assertTrue($environment->supportsColoredOutput());
    }

    public function testSupportsColoredOutputReturnsTrueIfConEmuAnsiIsUsed(): void {
        $environment = new WindowsEnvironment(['ConEmuANSI' => true]);
        $this->assertTrue($environment->supportsColoredOutput());
    }
}
