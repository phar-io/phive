<?php
namespace PharIo\Phive;

use PharIo\Version\Version;

class UnsupportedRelease implements Release {

    /** @var string */
    private $name;

    /** @var Version */
    private $version;

    /** @var string */
    private $reason;

    /**
     * @param string  $name
     * @param Version $version
     * @param string  $reason
     */
    public function __construct($name, Version $version, $reason) {
        $this->name = $name;
        $this->version = $version;
        $this->reason = $reason;
    }

    /**
     * @return bool
     */
    public function isSupported() {
        return false;
    }

    /**
     * @return Version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

}
