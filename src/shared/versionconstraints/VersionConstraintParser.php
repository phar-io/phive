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
                        $value,
                        [
                            new GreaterThanOrEqualToVersionConstraint($value, $version),
                            new SpecificMajorVersionConstraint($value, $version->getMajor()->getValue())
                        ]
                    );
            }

            $version = new Version($value);
            if ($version->getMajor()->isAny()) {
                return new AnyVersionConstraint();
            }
            if ($version->getMinor()->isAny()) {
                return new SpecificMajorVersionConstraint($value, $version->getMajor()->getValue());
            }
            if ($version->getPatch()->isAny()) {
                return new SpecificMajorAndMinorVersionConstraint($value, $version->getMajor()->getValue(), $version->getMinor()->getValue());
            }

            return new ExactVersionConstraint($value);
        }

    }

}

