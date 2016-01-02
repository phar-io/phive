<?php
namespace PharIo\Phive;

interface SignatureVerifier {

    /**
     * @param string $message
     * @param string $signature
     */
    public function verify($message, $signature);

}




