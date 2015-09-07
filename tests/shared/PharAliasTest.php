<?php
namespace PharIo\Phive {

    class PharAliasTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider valueProvider
         *
         * @param string $value
         */
        public function testValueHandling($value) {
            $alias = new PharAlias($value);
            $this->assertSame($value, (string)$alias);
        }

        /**
         * @return array
         */
        public function valueProvider() {
            return [
                ['foo'],
                ['bar']
            ];
        }

    }

}

