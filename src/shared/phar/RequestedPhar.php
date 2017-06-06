<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PharIo\Version\VersionConstraint;

class RequestedPhar {

    /**
     * @var PharIdentifier
     */
    private $identifier;

    /**
     * @var VersionConstraint
     */
    private $versionConstraint;

    /**
     * @var VersionConstraint
     */
    private $lockedVersion;

    /**
     * @var Filename|null
     */
    private $location;
    /**
     * @var bool
     */
    private $makeCopy;

    /**
     * @param PharIdentifier $identifier
     * @param VersionConstraint $versionConstraint
     * @param VersionConstraint $lockedVersion
     * @param Filename|null $location
     * @param bool $makeCopy
     */
    public function __construct(
        PharIdentifier $identifier,
        VersionConstraint $versionConstraint,
        VersionConstraint $lockedVersion,
        Filename $location = null,
        $makeCopy = false
    ) {
        $this->identifier = $identifier;
        $this->versionConstraint = $versionConstraint;
        $this->lockedVersion = $lockedVersion;
        $this->location = $location;
        $this->makeCopy = $makeCopy;
    }

    /**
     * @return PharIdentifier
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * @return PharAlias
     * @throws \Exception
     */
    public function getAlias() {
        if ($this->identifier instanceof PharAlias) {
            return $this->identifier;
        }
        throw new \Exception('Requested PHAR has no alias');
    }

    /**
     * @return PharUrl
     * @throws \Exception
     */
    public function getUrl() {
        if ($this->identifier instanceof PharUrl) {
            return $this->identifier;
        }
        throw new \Exception('Requested PHAR has no URL');
    }

    /**
     * @return bool
     */
    public function hasAlias() {
        return $this->identifier instanceof PharAlias;
    }

    /**
     * @return bool
     */
    public function hasUrl() {
        return $this->identifier instanceof PharUrl;
    }

    /**
     * @return VersionConstraint
     */
    public function getVersionConstraint() {
        return $this->versionConstraint;
    }

    /**
     * @return VersionConstraint
     */
    public function getLockedVersion() {
        return $this->lockedVersion;
    }

    /**
     * @return bool
     */
    public function hasLocation() {
        return $this->location !== null;
    }

    /**
     * @return Filename|null
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @return string
     */
    public function asString() {
        return $this->identifier->asString();
    }

    /**
     * @return bool
     */
    public function makeCopy() {
        return $this->makeCopy;
    }
}
