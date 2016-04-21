<?php
namespace PharIo\Phive;

class HttpProgressUpdate {

    /**
     * @var Url
     */
    private $url;

    /**
     * @var int
     */
    private $expectedDown;

    /**
     * @var int
     */
    private $received;

    /**
     * @var int
     */
    private $expectedUp;

    /**
     * @var int
     */
    private $sent;

    /**
     * @param Url $url
     * @param int $expectedDown
     * @param int $received
     * @param int $expectedUp
     * @param int $sent
     */
    public function __construct(Url $url, $expectedDown, $received, $expectedUp, $sent) {
        $this->url = $url;
        $this->expectedDown = $expectedDown;
        $this->received = $received;
        $this->expectedUp = $expectedUp;
        $this->sent = $sent;
    }

    /**
     * @return Url
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getExpectedDownloadSize() {
        return $this->expectedDown;
    }

    /**
     * @return int
     */
    public function getBytesReceived() {
        return $this->received;
    }

    /**
     * @return int
     */
    public function getExpectedUploadSize() {
        return $this->expectedUp;
    }

    /**
     * @return int
     */
    public function getBytesSent() {
        return $this->sent;
    }

    public function getDownloadPercent() {
        if ($this->expectedDown === 0) {
            return 0;
        }
        return round($this->received / ($this->expectedDown / 100));
    }

    public function getUploadPercent() {
        if ($this->expectedUp === 0) {
            return 0;
        }
        return round($this->sent / ($this->expectedUp / 100));
    }

}
