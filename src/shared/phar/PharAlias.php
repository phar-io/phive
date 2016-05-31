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
     * @param string            $name
     * @param VersionConstraint $versionConstraint
     */
    public function __construct($name, VersionConstraint $versionConstraint) {
        $this->name = $name;
        $this->versionConstraint = $versionConstraint;
    }

    /**
     * @return VersionConstraint
     */
    public function getVersionConstraint() {
        return $this->versionConstraint;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->name;
    }

}
