<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\RequestedPharAlias
 */
class RequestedPharAliasTest extends \PHPUnit_Framework_TestCase {

    public function testReturnsExpectedAlias() {
        $alias = new PharAlias('foo', new AnyVersionConstraint(), new AnyVersionConstraint());
        $phar = new RequestedPharAlias($alias);

        $this->assertTrue($phar->isAlias());
        $this->assertSame($alias, $phar->getAlias());
    }

}



