<?php
namespace PharIo\Phive {

    class PharAlias {

        /**
         * @var string
         */
        private $value = '';

        /**
         * @param string $value
         */
        public function __construct($value) {
            $this->value = $value;
        }

        /**
         * @return string
         */
        public function __toString() {
            return $this->value;
        }

    }

}

