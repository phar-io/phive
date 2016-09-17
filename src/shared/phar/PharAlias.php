<?php
namespace PharIo\Phive;

class PharAlias {

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var VersionConstraint
     */
    private $versionConstraint;

    /**
     * @var VersionConstraint
     */
    private $versionToInstall;

    /**
     * @param string            $name
     * @param VersionConstraint $versionConstraint
     * @param VersionConstraint $versionToInstall
     */
    public function __construct($name, VersionConstraint $versionConstraint, VersionConstraint $versionToInstall) {
        $this->name = $name;
        $this->versionConstraint = $versionConstraint;
        $this->versionToInstall = $versionToInstall;
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
    public function getVersionToInstall() {
        return $this->versionToInstall;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->name;
    }

}
