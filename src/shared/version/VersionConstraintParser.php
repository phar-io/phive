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
            return new ExactVersionConstraint($value);
        }

    }

}

