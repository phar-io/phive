<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\RequestedPharUrl
 */
class RequestedPharUrlTest extends \PHPUnit_Framework_TestCase {

    public function testReturnsExpectedUrl() {
        $url = new Url('https://example.com/foo.phar');
        $phar = new RequestedPharUrl($url);
        
        $this->assertFalse($phar->isAlias());
        $this->assertSame($url, $phar->getPharUrl());
    }

}



