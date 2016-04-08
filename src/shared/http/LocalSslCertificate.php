<?php
namespace PharIo\Phive;

class LocalSslCertificate {

    /**
     * @var string
     */
    private $hostname = '';

    /**
     * @var string
     */
    private $sourceFile = '';

    /**
     * @param string $hostname
     * @param string $sourceFile
     */
    public function __construct($hostname, $sourceFile) {
        $this->hostname = $hostname;
        $this->sourceFile = $sourceFile;
    }

    /**
     * @return string
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * @return string
     */
    public function getCertificateFile() {
        return $this->sourceFile;
    }

}