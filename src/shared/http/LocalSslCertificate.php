<?php declare(strict_types = 1);
namespace PharIo\Phive;

class LocalSslCertificate {

    /** @var string */
    private $hostname;

    /** @var resource */
    private $temporaryCertificateFile;

    public function __construct(string $hostname, string $sourceFile) {
        $this->hostname = $hostname;
        $this->createTemporaryCertificateFile($sourceFile);
    }

    public function getHostname(): string {
        return $this->hostname;
    }

    public function getCertificateFile(): string {
        return \stream_get_meta_data($this->temporaryCertificateFile)['uri'];
    }

    /**
     * @param string $sourceFile
     */
    private function createTemporaryCertificateFile($sourceFile): void {
        $this->temporaryCertificateFile = \tmpfile();
        \fwrite($this->temporaryCertificateFile, \file_get_contents($sourceFile));
    }
}
