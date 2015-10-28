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

            return new ExactVersionConstraint($value);
        }

    }

}

