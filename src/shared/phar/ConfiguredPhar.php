<?php
namespace PharIo\Phive;

class ConfiguredPhar {

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var VersionConstraint
     */
    private $versionConstraint;

    /**
     * @var Version|null
     */
    private $installedVersion;

    /**
     * @var Filename|null
     */
    private $location;

    /**
     * @var PharUrl|null
     */
    private $url;

    /**
     * @param string $name
     * @param VersionConstraint $versionConstraint
     * @param Version|null $installedVersion
     * @param Filename|null $location
     * @param PharUrl|null $url
     */
    public function __construct(
        $name,
        VersionConstraint $versionConstraint,
        Version $installedVersion = null,
        Filename $location = null,
        PharUrl $url = null
    ) {
        $this->name = $name;
        $this->versionConstraint = $versionConstraint;
        $this->installedVersion = $installedVersion;
        $this->location = $location;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return VersionConstraint
     */
    public function getVersionConstraint() {
        return $this->versionConstraint;
    }

    /**
     * @return Version|null
     */
    public function getInstalledVersion() {
        return $this->installedVersion;
    }

    /**
     * @return bool
     */
    public function isInstalled() {
        return $this->installedVersion !== null;
    }

    /**
     * @return bool
     */
    public function hasLocation() {
        return $this->location !== null;
    }

    /**
     * @return Filename
     */
    public function getLocation() {
        if (!$this->hasLocation()) {
            throw new ConfiguredPharException(
                'No location set',
                ConfiguredPharException::NoLocation
            );
        }
        return $this->location;
    }

    /**
     * @return bool
     */
    public function hasUrl() {
        return $this->url !== null;
    }

    /**
     * @return PharUrl
     */
    public function getUrl() {
        if (!$this->hasUrl()) {
            throw new ConfiguredPharException(
                'No URL set',
                ConfiguredPharException::NoUrl
            );
        }
        return $this->url;
    }
}
