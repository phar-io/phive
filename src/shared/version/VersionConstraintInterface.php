<?php
namespace PharIo\Phive {

    interface VersionConstraintInterface
    {

        /**
         * @param Version $version
         *
         * @return bool
         */
        public function complies(Version $version);

    }

}

