<?php
namespace TheSeer\Phive {

    class SignatureService {

        /**
         * @var SignatureVerifierInterface
         */
        private $signatureVerifier;

        /**
         * @param SignatureVerifierInterface $signatureVerifier
         */
        public function __construct(SignatureVerifierInterface $signatureVerifier) {
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

