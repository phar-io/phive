<?php
namespace PharIo\Phive {

    class GnupgVerificationResult {

        /**
         * @var array
         */
        private $verificationData = [];

        /**
         * @param array $data
         */
        public function __construct(array $data) {
            $this->validate($data);
            $this->verificationData = $data;
        }

        /**
         * @return string
         */
        public function getFingerprint() {
            return $this->verificationData['fingerprint'];
        }

        /**
         * @return bool
         */
        public function isKnownKey() {
            return ($this->verificationData['summary'] & 128) !== 128;
        }

        /**
         * @return bool
         */
        public function wasVerificationSuccessful() {
            return ($this->verificationData['summary'] == 0);
        }

        /**
         * @param array $keyinfo
         */
        private function validate(array $keyinfo) {
            if (!array_key_exists('summary', $keyinfo) || !array_key_exists('fingerprint', $keyinfo)) {
                throw new \InvalidArgumentException('Keyinfo does not contain required data');
            }
        }

    }

}

