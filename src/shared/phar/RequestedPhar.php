<?php
namespace PharIo\Phive;

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
     * @param PharIdentifier $identifier
     * @param VersionConstraint $versionConstraint
     * @param VersionConstraint $lockedVersion
     * @param Filename|null $location
     */
    public function __construct(
        PharIdentifier $identifier,
        VersionConstraint $versionConstraint,
        VersionConstraint $lockedVersion,
        Filename $location = null
    ) {
        $this->identifier = $identifier;
        $this->versionConstraint = $versionConstraint;
        $this->lockedVersion = $lockedVersion;
        $this->location = $location;
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
}
