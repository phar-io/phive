<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;

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
     * @var Filename
     */
    private $location;

    /**
     * @var bool
     */
    private $copy = false;

    /**
     * @param string            $name
     * @param Version           $installedVersion
     * @param VersionConstraint $versionConstraint
     * @param Filename          $location
     * @param bool              $copy
     */
    public function __construct(
        $name,
        Version $installedVersion,
        VersionConstraint $versionConstraint,
        Filename $location,
        $copy
    ) {
        $this->name = $name;
        $this->installedVersion = $installedVersion;
        $this->versionConstraint = $versionConstraint;
        $this->location = $location;
        $this->copy = $copy;
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
     * @return Filename
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @return bool
     */
    public function isCopy() {
        return $this->copy;
    }
}
