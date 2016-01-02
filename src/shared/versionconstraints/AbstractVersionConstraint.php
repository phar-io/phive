<?php
namespace PharIo\Phive {

    abstract class AbstractVersionConstraint implements VersionConstraint {
        /**
         * @var string
         */
        private $originalValue = '';

        /**
         * @param string $originalValue
         */
        public function __construct($originalValue) {
            $this->originalValue = $originalValue;
        }

        /**
         * @return string
         */
        public function asString() {
            return $this->originalValue;
        }

    }

}

