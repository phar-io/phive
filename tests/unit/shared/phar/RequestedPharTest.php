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

use Exception;
use PharIo\FileSystem\Filename;
use PharIo\Version\VersionConstraint;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\RequestedPhar
 */
class RequestedPharTest extends TestCase {
    public static function pharIdentifierProvider() {
        return [
            ['identifier' => new PharAlias('foo'), 'isAlias' => true],
            ['identifier' => new PharUrl('https://example.com'), 'isAlias' => false]
        ];
    }

    /**
     * @dataProvider pharIdentifierProvider
     *
     * @param bool $isAlias
     */
    public function testHasAliasReturnsExpectedValue(PharIdentifier $identifier, $isAlias): void {
        $phar = new RequestedPhar(
            $identifier,
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock()
        );

        $this->assertSame($isAlias, $phar->hasAlias());
    }

    /**
     * @dataProvider pharIdentifierProvider
     *
     * @param bool $isAlias
     */
    public function testHasUrlReturnsExpectedValue(PharIdentifier $identifier, $isAlias): void {
        $phar = new RequestedPhar(
            $identifier,
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock()
        );

        $this->assertNotSame($isAlias, $phar->hasUrl());
    }

    public function testAsStringReturnsExpectedValue(): void {
        $phar = new RequestedPhar(
            new PharAlias('foo'),
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock()
        );

        $this->assertSame('foo', $phar->asString());
    }

    public function testHasLocationReturnsFalseWhenNoLocationIsSet(): void {
        $phar = new RequestedPhar(
            new PharAlias('foo'),
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock()
        );

        $this->assertFalse($phar->hasLocation());
    }

    public function testHasLocationReturnsTrueWhenLocationIsSet(): void {
        $phar = new RequestedPhar(
            new PharAlias('foo'),
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock(),
            $this->getFilenameMock()
        );

        $this->assertTrue($phar->hasLocation());
    }

    public function testGetLocation(): void {
        $location = $this->getFilenameMock();
        $phar     = new RequestedPhar(
            new PharAlias('foo'),
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock(),
            $location
        );

        $this->assertSame($location, $phar->getLocation());
    }

    public function testGetLocationThrowsExceptionWhenNoLocationIsSet(): void {
        $phar = new RequestedPhar(
            new PharAlias('foo'),
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock()
        );
        $this->expectException(Exception::class);
        $phar->getLocation();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|VersionConstraint
     */
    private function getVersionConstraintMock() {
        return $this->createMock(VersionConstraint::class);
    }

    /**
     * @return Filename|PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilenameMock() {
        return $this->createMock(Filename::class);
    }
}
