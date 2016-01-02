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
    public function __construct($count, $fingerprint = null) {
        $this->count = $count;
        $this->fingerprint = $fingerprint;
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


