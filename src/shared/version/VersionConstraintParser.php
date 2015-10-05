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
                    return new VersionConstraintGroup(
                        [
                            new GreaterOrEqualThanVersionConstraint(new Version(substr($value, 1))),
                            new SpecificMajorVersionConstraint($this->getMajor($value))
                        ]
                    );
            }

            return new ExactVersionConstraint($value);
        }

        /**
         * @param string $value
         *
         * @return int
         */
        private function getMajor($value)
        {
            preg_match('/([0-9]*?)\./', $value, $matches);
            return (int)$matches[1];
        }

    }

}

