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

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Phar
 */
class PharTest extends TestCase {
    use ScalarTestDataProvider;

    /**
     * @dataProvider stringProvider
     *
     * @param string $name
     */
    public function testGetName($name): void {
        $phar = new Phar($name, new Version('1.0.0'), new File(new Filename('foo.phar'), ''));
        $this->assertSame($name, $phar->getName());
    }

    /**
     * @dataProvider versionProvider
     */
    public function testGetVersion(Version $version): void {
        $phar = new Phar('foo', $version, new File(new Filename('bar.phar'), ''));
        $this->assertEquals($version, $phar->getVersion());
    }

    /**
     * @dataProvider fileProvider
     */
    public function testGetFile(File $file): void {
        $phar = new Phar('foo', new Version('1.0.0'), $file);
        $this->assertEquals($file, $phar->getFile());
    }

    public function versionProvider(): array {
        return [
            [new Version('1.0.0')],
            [new Version('3.5.2')]
        ];
    }

    public function fileProvider() {
        return [
            [new File(new Filename('foo.phar'), 'bar')],
            [new File(new Filename('bar.phar'), 'baz')],
        ];
    }
}
