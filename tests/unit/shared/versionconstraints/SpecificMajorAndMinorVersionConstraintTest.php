<?php
namespace PharIo\Phive;

    /**
     * @covers PharIo\Phive\SpecificMajorAndMinorVersionConstraint
     */
    class SpecificMajorAndMinorVersionConstraintTest extends \PHPUnit_Framework_TestCase {

        public static function versionProvider() {
            return [
                // compliant versions
                [1, 0, new Version('1.0.2'), true],
                [1, 0, new Version('1.0.3'), true],
                [1, 1, new Version('1.1.1'), true],
                // non-compliant versions
                [2, 9, new Version('0.9.9'), false],
                [3, 2, new Version('2.2.3'), false],
                [2, 8, new Version('2.9.9'), false],
            ];
        }

        /**
         * @dataProvider versionProvider
         *
         * @param int     $major
         * @param int     $minor
         * @param Version $version
         * @param bool    $expectedResult
         */
        public function testReturnsTrueForCompliantVersions($major, $minor, Version $version, $expectedResult) {
            $constraint = new SpecificMajorAndMinorVersionConstraint('foo', $major, $minor);
            $this->assertSame($expectedResult, $constraint->complies($version));
        }

    }



