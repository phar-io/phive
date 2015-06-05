<?php
namespace TheSeer\Phive {

    interface SignatureVerifierInterface {

        /**
         * @param string $message
         * @param string $signature
         */
        public function verify($message, $signature);

    }

}


