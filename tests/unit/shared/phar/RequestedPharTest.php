<?php
namespace PharIo\Phive;

use PharIo\Version\VersionConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\RequestedPhar
 */
class RequestedPharTest extends TestCase {

    /**
     * @dataProvider pharIdentifierProvider
     *
     * @param PharIdentifier $identifier
     * @param bool $isAlias
     */
    public function testHasAliasReturnsExpectedValue(PharIdentifier $identifier, $isAlias) {
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
     * @param PharIdentifier $identifier
     * @param bool $isAlias
     */
    public function testHasUrlReturnsExpectedValue(PharIdentifier $identifier, $isAlias) {
        $phar = new RequestedPhar(
            $identifier,
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock()
        );

        $this->assertSame(!$isAlias, $phar->hasUrl());
    }

    public function testAsStringReturnsExpectedValue() {
        $phar = new RequestedPhar(
            new PharAlias('foo'),
            $this->getVersionConstraintMock(),
            $this->getVersionConstraintMock()
        );

        $this->assertSame('foo', $phar->asString());
    }

    public static function pharIdentifierProvider() {
        return [
            ['identifier' => new PharAlias('foo'), 'isAlias' => true],
            ['identifier' => new PharUrl('https://example.com'), 'isAlias' => false]
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|VersionConstraint
     */
    private function getVersionConstraintMock() {
        return $this->createMock(VersionConstraint::class);
    }

}
