<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HttpProgressUpdate {

    /** @var Url */
    private $url;

    /** @var int */
    private $expectedDown;

    /** @var int */
    private $received;

    /** @var int */
    private $expectedUp;

    /** @var int */
    private $sent;

    public function __construct(Url $url, int $expectedDown, int $received, int $expectedUp, int $sent) {
        $this->url          = $url;
        $this->expectedDown = $expectedDown;
        $this->received     = $received;
        $this->expectedUp   = $expectedUp;
        $this->sent         = $sent;
    }

    public function getUrl(): Url {
        return $this->url;
    }

    public function getExpectedDownloadSize(): int {
        return $this->expectedDown;
    }

    public function getBytesReceived(): int {
        return $this->received;
    }

    public function getExpectedUploadSize(): int {
        return $this->expectedUp;
    }

    public function getBytesSent(): int {
        return $this->sent;
    }

    public function getDownloadPercent() {
        if ($this->expectedDown === 0) {
            return 0;
        }

        return \round($this->received / ($this->expectedDown / 100));
    }

    public function getUploadPercent() {
        if ($this->expectedUp === 0) {
            return 0;
        }

        return \round($this->sent / ($this->expectedUp / 100));
    }
}
