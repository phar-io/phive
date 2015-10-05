<?php
namespace PharIo\Phive {

    interface VersionConstraintInterface
    {

        /**
         * @param Version $version
         *
         * @return bool
         */
        public function matches(Version $version);

    }

}

