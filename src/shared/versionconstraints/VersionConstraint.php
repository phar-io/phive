<?php
namespace PharIo\Phive {

    interface VersionConstraint {

        /**
         * @param Version $version
         *
         * @return bool
         */
        public function complies(Version $version);

        /**
         * @return string
         */
        public function asString();

    }

}

