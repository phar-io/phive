<?php
namespace PharIo\Phive {

    class SignatureService {

        /**
         * @var SignatureVerifier
         */
        private $signatureVerifier;

        /**
         * @param SignatureVerifier $signatureVerifier
         */
        public function __construct(SignatureVerifier $signatureVerifier) {
            $this->signatureVerifier = $signatureVerifier;
        }

        /**
         * @param string $message
         * @param string $signature
         *
         * @return GnupgVerificationResult
         */
        public function verify($message, $signature) {
            return $this->signatureVerifier->verify($message, $signature);
        }

    }

}

