<?php
namespace PharIo\Phive {

    class ExactVersionConstraint implements VersionConstraintInterface
    {
        /**
         * @var string
         */
        private $value = '';

        /**
         * @param string $value
         */
        public function __construct($value)
        {
            $this->value = $value;
        }

        /**
         * @param Version $version
         *
         * @return bool
         */
        public function matches(Version $version)
        {
            return $this->value == $version->getVersionString();
        }
    }

}

