<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\Manifest\Manifest;
use PharIo\Manifest\ManifestLoader;
use PharIo\Manifest\ManifestLoaderException;
use PharIo\Version\Version;

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
     * @return string
     *
     * @throws PharException
     */
    public function getSignatureFingerprint() {
        if (!$this->hasSignatureFingerprint()) {
            throw new PharException('No signature fingerprint set');
        }
        return $this->signatureFingerprint;
    }

    /**
     * @return bool
     */
    public function hasManifest() {
        return file_exists(
            'phar://' .
            $this->file->getFilename()->asString() .
            '/manifest.xml'
        );
    }

    /**
     * @return Manifest
     *
     * @throws PharException
     */
    public function getManifest() {
        try {
            return ManifestLoader::fromPhar($this->file->getFilename());
        } catch (ManifestLoaderException $e) {
            throw new PharException("Loading manifest failed.", 0, $e);
        }
    }
}
