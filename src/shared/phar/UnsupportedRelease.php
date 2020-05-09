<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Version\Version;

class UnsupportedRelease implements Release {

    /** @var string */
    private $name;

    /** @var Version */
    private $version;

    /** @var string */
    private $reason;

    public function __construct(string $name, Version $version, string $reason) {
        $this->name    = $name;
        $this->version = $version;
        $this->reason  = $reason;
    }

    public function isSupported(): bool {
        return false;
    }

    public function getVersion(): Version {
        return $this->version;
    }

    public function getReason(): string {
        return $this->reason;
    }

    public function getName(): string {
        return $this->name;
    }
}
