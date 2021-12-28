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

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\PharRegistry
 */
class PharRegistryTest extends TestCase {
    protected function setUp(): void {
        TestStreamWrapper::register('test', __DIR__ . '/fixtures/');
    }

    protected function tearDown(): void {
        TestStreamWrapper::unregister();
    }

    public function testReturnsExpectedUnusedPhars(): void {
        $repo = new PharRegistry(
            new XmlFile(
                new Filename(__DIR__ . '/fixtures/phars.xml'),
                'https://phar.io/phive/installdb',
                'phars'
            ),
            new Directory(__DIR__ . '/fixtures')
        );

        $expected = [
            new Phar('phpunit', new Version('4.8.7'), new File(new Filename('test://phars/phpunit-4.8.7.phar.dummy'), 'phpunit-4.8.7')),
            new Phar('phpunit', new Version('4.8.6'), new File(new Filename('test://phars/phpunit-4.8.6.phar.dummy'), 'phpunit-4.8.6')),
        ];
        $actual = $repo->getUnusedPhars();

        $this->assertEquals($expected, $actual);
    }

    public function testReturnsExpectedUsedPharsByDestination(): void {
        $repo = new PharRegistry(
            new XmlFile(
                new Filename(__DIR__ . '/fixtures/phars.xml'),
                'https://phar.io/phive/installdb',
                'phars'
            ),
            new Directory(__DIR__ . '/fixtures')
        );

        /** @var Directory|PHPUnit_Framework_MockObject_MockObject $destination */
        $destination = $this->createMock(Directory::class);
        $destination->method('asString')
            ->willReturn('/vagrant/phive/tools');

        $expected = [
            new Phar('phpab', new Version('1.20.0'), new File(new Filename('test://phars/phpab-1.20.0.phar.dummy'), 'phpab-1.20.0')),
            new Phar('phpunit', new Version('5.2.10'), new File(new Filename('test://phars/phpunit-5.2.10.phar.dummy'), 'phpunit-5.2.10')),
        ];
        $actual = $repo->getUsedPharsByDestination($destination);

        $this->assertEquals($expected, $actual);
    }

    public function testReturnsExpectedFingerprints(): void {
        $repo = new PharRegistry(
            new XmlFile(
                new Filename(__DIR__ . '/fixtures/phars.xml'),
                'https://phar.io/phive/installdb',
                'phars'
            ),
            new Directory(__DIR__ . '/fixtures')
        );

        $expected = [
            'E8406D0D82947747293778314AA394086372C20A',
            'D8406D0D82947747293778314AA394086372C20A'
        ];
        $actual = $repo->getKnownSignatureFingerprints('phpunit');

        $this->assertEquals($expected, $actual);
    }
}
