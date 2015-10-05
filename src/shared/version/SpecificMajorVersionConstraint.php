<?php
namespace PharIo\Phive {

    class SpecificMajorVersionConstraint implements VersionConstraintInterface
    {
        /**
         * @var int
         */
        private $major = 0;

        /**
         * @param int $major
         */
        public function __construct($major) {
            $this->major = $major;
        }

        /**
         * @param Version $version
         *
         * @return bool
         */
        public function complies(Version $version)
        {
            return $version->getMajor() == $this->major;
        }

    }

}

