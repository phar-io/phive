<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PharIo\Version\VersionConstraint;

class RequestedPhar {
    /** @var PharIdentifier */
    private $identifier;

    /** @var VersionConstraint */
    private $versionConstraint;

    /** @var VersionConstraint */
    private $lockedVersion;

    /** @var null|Filename */
    private $location;

    /** @var bool */
    private $makeCopy;

    public function __construct(
        PharIdentifier $identifier,
        VersionConstraint $versionConstraint,
        VersionConstraint $lockedVersion,
        Filename $location = null,
        bool $makeCopy = false
    ) {
        $this->identifier        = $identifier;
        $this->versionConstraint = $versionConstraint;
        $this->lockedVersion     = $lockedVersion;
        $this->location          = $location;
        $this->makeCopy          = $makeCopy;
    }

    public function getIdentifier(): PharIdentifier {
        return $this->identifier;
    }

    /**
     * @throws \Exception
     */
    public function getAlias(): PharAlias {
        if ($this->identifier instanceof PharAlias) {
            return $this->identifier;
        }

        throw new \Exception('Requested PHAR has no alias');
    }

    /**
     * @throws \Exception
     */
    public function getUrl(): PharUrl {
        if ($this->identifier instanceof PharUrl) {
            return $this->identifier;
        }

        throw new \Exception('Requested PHAR has no URL');
    }

    public function hasAlias(): bool {
        return $this->identifier instanceof PharAlias;
    }

    public function hasUrl(): bool {
        return $this->identifier instanceof PharUrl;
    }

    public function getVersionConstraint(): VersionConstraint {
        return $this->versionConstraint;
    }

    public function getLockedVersion(): VersionConstraint {
        return $this->lockedVersion;
    }

    /** @psalm-assert-if-true Filename $this->location */
    public function hasLocation(): bool {
        return $this->location !== null;
    }

    public function getLocation(): ?Filename {
        return $this->location;
    }

    public function asString(): string {
        return $this->identifier->asString();
    }

    public function makeCopy(): bool {
        return $this->makeCopy;
    }
}
