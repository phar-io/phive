<?php
namespace PharIo\Phive {

    class PharAliasTest extends \PHPUnit_Framework_TestCase {

        use ScalarTestDataProvider;

        /**
         * @dataProvider stringProvider
         *
         * @param string $value
         */
        public function testValueHandling($value) {
            $alias = new PharAlias($value);
            $this->assertSame($value, (string)$alias);
        }

    }

}

