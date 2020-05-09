<?php declare(strict_types = 1);
namespace PharIo\Phive;

class StaticPhiveVersion extends PhiveVersion {
    /** @var string */
    private $version;

    public function __construct(string $version) {
        $this->version = $version;
    }

    public function getVersion(): string {
        return $this->version;
    }
}
