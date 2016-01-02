<?php
namespace PharIo\Phive {

    class UnsupportedHashStub implements Hash {

        /**
         * @return string
         */
        public function asString() {
            return 'foo';
        }

        /**
         * @param Hash $otherHash
         *
         * @return bool
         */
        public function equals(Hash $otherHash) {
            return false;
        }

    }

}
