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
 * @covers \PharIo\Phive\EnvironmentLocator
 */
class EnvironmentLocatorTest extends TestCase {
    /**
     * @dataProvider osProvider
     *
     * @param string $operatingSystem
     * @param string $expectedClass
     */
    public function testReturnsExpectedEnvironment($operatingSystem, $expectedClass): void {
        $locator     = new EnvironmentLocator();
        $environment = $locator->getEnvironment($operatingSystem);

        $this->assertInstanceOf($expectedClass, $environment);
    }

    public function osProvider() {
        return [
            ['WINNT', WindowsEnvironment::class],
            ['WIN32', WindowsEnvironment::class],
            ['Windows', WindowsEnvironment::class],
            ['Darwin', UnixoidEnvironment::class],
            ['FreeBSD', UnixoidEnvironment::class],
            ['Linux', UnixoidEnvironment::class],
            ['SunOS', UnixoidEnvironment::class],
            ['Unix', UnixoidEnvironment::class]
        ];
    }
}
