<?php
namespace PharIo\Phive;

class InstalledPhar {

    /**
     * @var string
     */
    private $name;

    /**
     * @var Version
     */
    private $installedVersion;

    /**
     * @var VersionConstraint
     */
    private $versionConstraint;

    /**
     * @var Directory
     */
    private $location;

    /**
     * InstalledPhar constructor.
     *
     * @param string $name
     * @param Version $installedVersion
     * @param VersionConstraint $versionConstraint
     * @param Directory $location
     */
    public function __construct(
        $name,
        Version $installedVersion,
        VersionConstraint $versionConstraint,
        Directory $location
    ) {
        $this->name = $name;
        $this->installedVersion = $installedVersion;
        $this->versionConstraint = $versionConstraint;
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Version
     */
    public function getInstalledVersion() {
        return $this->installedVersion;
    }

    /**
     * @return VersionConstraint
     */
    public function getVersionConstraint() {
        return $this->versionConstraint;
    }

    /**
     * @return Directory
     */
    public function getLocation() {
        return $this->location;
    }
}