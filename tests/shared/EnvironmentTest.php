<?php
namespace PharIo\Phive {

    class EnvironmentTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider hasProxyProvider
         *
         * @param string $server
         * @param bool $expected
         */
        public function testHasProxy($server, $expected) {
            $env = new Environment($server);
            $this->assertSame($expected, $env->hasProxy());
        }

        public function hasProxyProvider() {
            return [
                [[], false],
                [['https_proxy' => 'foo'], true]
            ];
        }

        /**
         * @dataProvider getProxyProvider
         *
         * @param string $proxy
         */
        public function testGetProxy($proxy) {
            $env = new Environment(['https_proxy' => $proxy]);
            $this->assertSame($proxy, $env->getProxy());
        }

        public function getProxyProvider() {
            return [
                ['https://proxy.example.com:8080'],
                ['http://proxy.domain.tld'],
            ];
        }

        /**
         *
         */
        public function testGetHomeDirectory() {
            $env = new Environment(['HOME' => __DIR__]);
            $this->assertSame(__DIR__, (string)$env->getHomeDirectory());
        }

        /**
         * @expectedException \BadMethodCallException
         */
        public function testGetProxyThrowsExceptionIfProxyIsNotSet() {
            $env = new Environment([]);
            $env->getProxy();
        }

        /**
         * @expectedException \BadMethodCallException
         */
        public function testGetHomeDirectoryThrowsExceptionIfHomeIsNotSet() {
            $env = new Environment([]);
            $env->getHomeDirectory();
        }

    }

}

