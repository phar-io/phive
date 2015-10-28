<?php
namespace PharIo\Phive {

    class SpecificMajorAndMinorVersionConstraint implements VersionConstraintInterface
    {
        /**
         * @var int
         */
        private $major = 0;

        /**
         * @var int
         */
        private $minor = 0;

        /**
         * @param int $major
         * @param int $minor
         */
        public function __construct($major, $minor) {
            $this->major = $major;
            $this->minor = $minor;
        }

        /**
         * @param Version $version
         *
         * @return bool
         */
        public function complies(Version $version)
        {
            if ($version->getMajor()->getValue() != $this->major) {
                return false;
            }
            return $version->getMinor()->getValue() == $this->minor;
        }

    }

}

