<?php
namespace PharIo\Phive;

    /**
     * @covers PharIo\Phive\VersionConstraintParser
     */
    class VersionConstraintParserTest extends \PHPUnit_Framework_TestCase {

        public static function versionStringProvider() {
            return [
                ['1.0.2', new ExactVersionConstraint('1.0.2')],
                [
                    '~4.6',
                    new VersionConstraintGroup(
                        '~4.6',
                        [
                            new GreaterThanOrEqualToVersionConstraint('~4.6', new Version('4.6')),
                            new SpecificMajorVersionConstraint('~4.6', 4)
                        ]
                    )
                ],
                ['5.1.*', new SpecificMajorAndMinorVersionConstraint('5.1.*', 5, 1)],
                ['5.*', new SpecificMajorVersionConstraint('5.*', 5)],
                ['*', new AnyVersionConstraint()]
            ];
        }

        /**
         * @dataProvider versionStringProvider
         *
         * @param string            $versionString
         * @param VersionConstraint $expectedConstraint
         */
        public function testReturnsExpectedConstraint($versionString, VersionConstraint $expectedConstraint) {
            $parser = new VersionConstraintParser();
            $this->assertEquals($expectedConstraint, $parser->parse($versionString));
        }

    }



