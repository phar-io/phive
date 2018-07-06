<?php
namespace PharIo\Phive;

use PharIo\Version\Version;

class SupportedRelease implements Release {

    /**
     * @var Version
     */
    private $version;

    /**
     * @var PharUrl
     */
    private $url;

    /**
     * @var Hash
     */
    private $expectedHash;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var Url
     */
    private $signatureUrl;

    /**
     * @param string  $name
     * @param Version $version
     * @param PharUrl $url
     * @param Url     $signatureUrl
     * @param Hash    $expectedHash
     */
    public function __construct($name, Version $version, PharUrl $url, Url $signatureUrl = null, Hash $expectedHash = null) {
        $this->name = $name;
        $this->version = $version;
        $this->url = $url;
        $this->signatureUrl = $signatureUrl;
        $this->expectedHash = $expectedHash;
    }

    public function isSupported() {
        return true;
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
     * @return PharUrl
     */
    public function getUrl() {
        return $this->url;
    }

    public function hasSignatureUrl() {
        return $this->signatureUrl !== null;
    }

    /**
     * @return Url
     */
    public function getSignatureUrl() {
        return $this->signatureUrl;
    }

    /**
     * @return bool
     */
    public function hasExpectedHash() {
        return $this->expectedHash !== null;
    }

    /**
     * @return Hash
     */
    public function getExpectedHash() {
        return $this->expectedHash;
    }

}
