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
            $alias = new PharAlias($value, new AnyVersionConstraint());
            $this->assertSame($value, (string)$alias);
        }

        /**
         * @dataProvider versionConstraintProvider
         *
         * @param VersionConstraintInterface $constraint
         */
        public function testGetVersionConstraint(VersionConstraintInterface $constraint) {
            $alias = new PharAlias('foo', $constraint);
            $this->assertSame($constraint, $alias->getVersionConstraint());
        }

        /**
         * @return array
         */
        public static function versionConstraintProvider() {
            return [
                [new AnyVersionConstraint()],
                [new ExactVersionConstraint('1.0.0')]
            ];
        }

    }

}

