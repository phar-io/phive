<?php
namespace PharIo\Phive {

    class PharAlias {

        /**
         * @var string
         */
        private $name = '';

        /**
         * @var ExactVersionConstraint
         */
        private $versionConstraint;

        /**
         * @param string                     $name
         * @param VersionConstraintInterface $versionConstraint
         */
        public function __construct($name, VersionConstraintInterface $versionConstraint) {
            $this->name = $name;
            $this->versionConstraint = $versionConstraint;
        }

        /**
         * @return ExactVersionConstraint
         */
        public function getVersionConstraint() {
            return $this->versionConstraint;
        }

        /**
         * @return string
         */
        public function __toString() {
            return $this->name;
        }

    }

}

