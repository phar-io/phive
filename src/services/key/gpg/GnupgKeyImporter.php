<?php
namespace TheSeer\Phive {

    class GnupgKeyImporter implements KeyImporter {

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
         * @param string $key
         */
        public function importKey($key) {
            return $this->gnupg->import($key);
        }

    }

}

