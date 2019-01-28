<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

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
