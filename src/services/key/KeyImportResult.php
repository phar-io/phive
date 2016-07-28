<?php
namespace PharIo\Phive;

class KeyImportResult {

    /**
     * @var int
     */
    private $count;

    /**
     * @var string
     */
    private $fingerprint;

    /**
     * @param int    $count
     * @param string $fingerprint
     */
    public function __construct($count, $fingerprint = '') {
        $this->count = $count;
        $this->fingerprint = $fingerprint;
    }

    /**
     * @return bool
     */
    public function isSuccess() {
        return $this->getCount() !== 0;
    }

    /**
     * @return int
     */
    public function getCount() {
        return $this->count;
    }

    /**
     * @return string
     */
    public function getFingerprint() {
        return $this->fingerprint;
    }

}
