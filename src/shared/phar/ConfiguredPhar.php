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
     * @param string $name
     * @param VersionConstraint $versionConstraint
     * @param Version|null $installedVersion
     */
    public function __construct(
        $name,
        VersionConstraint $versionConstraint,
        Version $installedVersion = null
    ) {
        $this->name = $name;
        $this->versionConstraint = $versionConstraint;
        $this->installedVersion = $installedVersion;
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

}