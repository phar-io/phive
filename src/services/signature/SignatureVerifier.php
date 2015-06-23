<?php
namespace TheSeer\Phive {

    interface SignatureVerifier {

        /**
         * @param string $message
         * @param string $signature
         */
        public function verify($message, $signature);

    }

}


