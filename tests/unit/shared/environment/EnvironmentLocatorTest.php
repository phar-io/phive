<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

class EnvironmentLocatorTest extends TestCase {

    /**
     * @dataProvider osProvider
     *
     * @param string $operatingSystem
     * @param string $expectedClass
     */
    public function testReturnsExpectedEnvironment($operatingSystem, $expectedClass) {
        $locator = new EnvironmentLocator();
        $environment = $locator->getEnvironment($operatingSystem);

        $this->assertInstanceOf($expectedClass, $environment);
    }

    public function osProvider() {
        return [
            ['WINNT', WindowsEnvironment::class],
            ['WIN32', WindowsEnvironment::class],
            ['Windows', WindowsEnvironment::class],
            ['Darwin', MacOsEnvironment::class],
            ['FreeBSD', UnixoidEnvironment::class],
            ['Linux', UnixoidEnvironment::class],
            ['SunOS', UnixoidEnvironment::class],
            ['Unix', UnixoidEnvironment::class]
        ];
    }
}
