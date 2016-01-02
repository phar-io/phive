<?php
namespace PharIo\Phive {

    class VersionConstraintGroup extends AbstractVersionConstraint {

        /**
         * @var VersionConstraint[]
         */
        private $constraints = [];

        /**
         * @param string              $originalValue
         * @param VersionConstraint[] $constraints
         */
        public function __construct($originalValue, array $constraints) {
            parent::__construct($originalValue);
            $this->constraints = $constraints;
        }

        /**
         * @param Version $version
         *
         * @return bool
         */
        public function complies(Version $version) {
            foreach ($this->constraints as $constraint) {
                if (!$constraint->complies($version)) {
                    return false;
                }
            }
            return true;
        }

    }

}

