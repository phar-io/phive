<?php
namespace TheSeer\Phive {

    /**
     * GPG Signature Verification using the GnuPG PECL Extension.
     */
    class GnupgSignatureVerifier implements SignatureVerifier {

        /**
         * @var \Gnupg
         */
        private $gpg;

        /**
         * @param \Gnupg $gpg
         */
        public function __construct(\Gnupg $gpg) {
            $this->gpg = $gpg;
        }

        /**
         * @param string $message
         * @param string $signature
         *
         * @return GnupgVerificationResult
         * @throws VerificationFailedException
         */
        public function verify($message, $signature) {
            try {
                return new GnupgVerificationResult($this->gpg->verify($message, $signature)[0]);
            } catch (\Exception $e) {
                throw new VerificationFailedException($e->getMessage(), $e->getCode(), $e);
            }
        }

    }

}

