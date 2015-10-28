<?php
namespace PharIo\Phive {

    class VersionConstraintGroup implements VersionConstraintInterface
    {
        /**
         * @var VersionConstraintInterface[]
         */
        private $constraints = [];

        /**
         * @param VersionConstraintInterface[] $constraints
         */
        public function __construct(array $constraints)
        {
            $this->constraints = $constraints;
        }

        /**
         * @param Version $version
         *
         * @return bool
         */
        public function complies(Version $version)
        {
            foreach ($this->constraints as $constraint) {
                if (!$constraint->complies($version)) {
                    return false;
                }
            }
            return true;
        }

    }

}

