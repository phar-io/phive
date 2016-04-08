<?php
namespace PharIo\Phive;

class LocalSslCertificate {

    /**
     * @var string
     */
    private $hostname = '';

    /**
     * @var resource
     */
    private $temporaryCertificateFile;

    /**
     * @param string $hostname
     * @param string $sourceFile
     */
    public function __construct($hostname, $sourceFile) {
        $this->hostname = $hostname;
        $this->createTemporaryCertificateFile($sourceFile);
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
        return stream_get_meta_data($this->temporaryCertificateFile)['uri'];
    }

    /**
     * @param string $sourceFile
     */
    private function createTemporaryCertificateFile($sourceFile) {
        $this->temporaryCertificateFile = tmpfile();
        fwrite($this->temporaryCertificateFile, file_get_contents($sourceFile));
    }

}