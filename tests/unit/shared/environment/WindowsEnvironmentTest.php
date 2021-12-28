<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
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
