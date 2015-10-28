<?php
namespace PharIo\Phive {

    class VersionConstraintParser
    {

        /**
         * @param string $value
         *
         * @return ExactVersionConstraint
         */
        public function parse($value) {

            switch($value[0]) {
                case '~':
                    $version = new Version(substr($value, 1));
                    return new VersionConstraintGroup(
                        [
                            new GreaterThanOrEqualToVersionConstraint($version),
                            new SpecificMajorVersionConstraint($version->getMajor())
                        ]
                    );
            }

            $version = new Version($value);
            if ($version->getMajor() === '*') {
                return new AnyVersionConstraint();
            }
            if ($version->getMinor() === '*') {
                return new SpecificMajorVersionConstraint($version->getMajor());
            }
            if ($version->getPatch() === '*') {
                return new SpecificMajorAndMinorVersionConstraint($version->getMajor(), $version->getMinor());
            }

            return new ExactVersionConstraint($value);
        }

    }

}

