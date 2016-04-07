<?php
namespace PharIo\Phive;

interface VerificationResult {

    /**
     * @return string
     */
    public function getFingerprint();

    /**
     * @return bool
     */
    public function isKnownKey();

    /**
     * @return bool
     */
    public function wasVerificationSuccessful();
}
