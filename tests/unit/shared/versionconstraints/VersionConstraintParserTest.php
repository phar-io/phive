<?php
namespace PharIo\Phive {

    /**
     * @covers PharIo\Phive\VersionConstraintParser
     */
    class VersionConstraintParserTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider versionStringProvider
         *
         * @param string                     $versionString
         * @param VersionConstraintInterface $expectedConstraint
         */
        public function testReturnsExpectedConstraint($versionString, VersionConstraintInterface $expectedConstraint) {
            $parser = new VersionConstraintParser();
            $this->assertEquals($expectedConstraint, $parser->parse($versionString));
        }

        public static function versionStringProvider() {
            return [
                ['1.0.2', new ExactVersionConstraint('1.0.2')],
                [
                    '~4.6',
                    new VersionConstraintGroup(
                        [
                            new GreaterThanOrEqualToVersionConstraint(new Version('4.6')),
                            new SpecificMajorVersionConstraint(4)
                        ]
                    )
                ],
                ['5.1.*', new SpecificMajorAndMinorVersionConstraint(5, 1)],
                ['5.*', new SpecificMajorVersionConstraint(5)],
                ['*', new AnyVersionConstraint()]
            ];
        }

    }

}

