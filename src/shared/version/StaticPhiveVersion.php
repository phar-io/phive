<?php
namespace PharIo\Phive;

class StaticPhiveVersion extends PhiveVersion {

    /**
     * @var string
     */
    private $version;

    /**
     * @param string $version
     */
    public function __construct($version) {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

}
