<?php
namespace PharIo\Phive {

    /**
     * @covers PharIo\Phive\PharIoRepositoryList
     */
    class PharIoRepositoryListTest extends \PHPUnit_Framework_TestCase {

        public function testReturnsEmptyArrayIfFileDoesNotExist() {
            $list = new PharIoRepositoryList('php://memory/foo.xml');
            $this->assertEquals([], $list->getRepositoryUrls(new PharAlias('bar', new AnyVersionConstraint())));
        }

        public function testReturnsEmptyArrayForUnknownAlias() {
            $list = new PharIoRepositoryList(__DIR__ . '/../data/repositories.xml');
            $this->assertEquals([], $list->getRepositoryUrls(new PharAlias('foo', new AnyVersionConstraint())));
        }

        public function testReturnsExpectedArrayOfUrls() {
            $list = new PharIoRepositoryList(__DIR__ . '/../../data/repositories.xml');

            $expected = [new Url('https://phar.phpunit.de'), new Url('https://phar.io')];
            $this->assertEquals($expected, $list->getRepositoryUrls(new PharAlias('phpunit', new AnyVersionConstraint())));

            $expected = [new Url('https://phar.io')];
            $this->assertEquals($expected, $list->getRepositoryUrls(new PharAlias('phpab', new AnyVersionConstraint())));
        }

    }

}

