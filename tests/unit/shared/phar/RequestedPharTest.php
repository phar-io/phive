<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\RequestedPhar
 */
class RequestedPharTest extends \PHPUnit_Framework_TestCase {

    public function testCreatesInstanceFromUrl() {
        $url = new Url('https://example.com/foo.phar');
        $phar = RequestedPhar::fromUrl($url);

        $this->assertInstanceOf(RequestedPhar::class, $phar);
        $this->assertFalse($phar->isAlias());
        $this->assertSame($url, $phar->getPharUrl());
    }

    public function testCreatesInstanceFromAlias() {
        $alias = new PharAlias('foo', new AnyVersionConstraint());
        $phar = RequestedPhar::fromAlias($alias);

        $this->assertInstanceOf(RequestedPhar::class, $phar);
        $this->assertTrue($phar->isAlias());
        $this->assertSame($alias, $phar->getAlias());
    }

}



