<?php
namespace PharIo\Phive;

class Phar {

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var Version
     */
    private $version;

    /**
     * @var File
     */
    private $file;

    /**
     * @var string|null
     */
    private $signatureFingerprint;

    /**
     * @param string      $name
     * @param Version     $version
     * @param File        $file
     * @param string|null $signatureFingerprint
     */
    public function __construct($name, Version $version, File $file, $signatureFingerprint = null) {
        $this->name = $name;
        $this->file = $file;
        $this->version = $version;
        $this->signatureFingerprint = $signatureFingerprint;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return File
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function hasSignatureFingerprint() {
        return $this->signatureFingerprint !== null;
    }

    /**
     * @return null|string
     */
    public function getSignatureFingerprint() {
        return $this->signatureFingerprint;
    }

}
