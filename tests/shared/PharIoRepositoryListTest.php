<?php
namespace PharIo\Phive {

    class PharIoRepositoryListTest extends \PHPUnit_Framework_TestCase {

        public function testReturnsEmptyArrayForUnknownAlias() {
            $list = new PharIoRepositoryList(__DIR__ . '/../data/repositories.xml');

            $expected = [];
            $this->assertEquals($expected, $list->getRepositoryUrls(new PharAlias('foo')));
        }

        public function testReturnsExpectedArrayOfUrls() {
            $list = new PharIoRepositoryList(__DIR__ . '/../data/repositories.xml');

            $expected = [
                new Url('https://phar.phpunit.de'),
                new Url('https://phar.io')
            ];
            $this->assertEquals($expected, $list->getRepositoryUrls(new PharAlias('phpunit')));

            $expected = [
                new Url('https://phar.io')
            ];
            $this->assertEquals($expected, $list->getRepositoryUrls(new PharAlias('phpab')));
        }

    }

}

