<?php
namespace TheSeer\Phive {

    class GnupgKeyRing implements KeyRingInterface {

        /**
         * @var \Gnupg
         */
        private $gnupg;

        /**
         * @param \Gnupg $gnupg
         */
        public function __construct(\Gnupg $gnupg) {
            $this->gnupg = $gnupg;
        }

        /**
         * @param string $keyId
         * @return bool
         */
        public function hasKey($keyId) {
            return !empty($this->gnupg->keyinfo($keyId));
        }

    }

}

