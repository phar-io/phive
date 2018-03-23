<?php
namespace PharIo\Phive;

class HostEntry {

    /** @var string */
    private $hostname;

    /** @var string */
    private $ipAddress;

    /**
     * HostEntry constructor.
     *
     * @param string $hostname
     * @param string $ipAddress
     */
    public function __construct($hostname, $ipAddress) {
        $this->hostname = $hostname;
        $this->ipAddress = $ipAddress;
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
    public function getIpAddress() {
        return $this->ipAddress;
    }

}
