<?php
namespace PharIo\Phive;

class Release {

    /**
     * @var Version
     */
    private $version;

    /**
     * @var Url
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
     * @param string $name
     * @param Version $version
     * @param Url $url
     * @param Hash $expectedHash
     */
    public function __construct($name, Version $version, Url $url, Hash $expectedHash = null) {
        $this->version = $version;
        $this->url = $url;
        $this->expectedHash = $expectedHash;
        $this->name = $name;
    }

    /**
     * @return Version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return Url
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return Hash
     */
    public function getExpectedHash() {
        return $this->expectedHash;
    }

    /**
     * @return bool
     */
    public function hasExpectedHash() {
        return null !== $this->expectedHash;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
}
