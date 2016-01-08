<?php
namespace PharIo\Phive;

class GreaterThanOrEqualToVersionConstraint extends AbstractVersionConstraint {

    /**
     * @var Version
     */
    private $minimalVersion;

    /**
     * @param string  $originalValue
     * @param Version $minimalVersion
     */
    public function __construct($originalValue, Version $minimalVersion) {
        parent::__construct($originalValue);
        $this->minimalVersion = $minimalVersion;
    }

    /**
     * @param Version $version
     *
     * @return bool
     */
    public function complies(Version $version) {
        return $version->getVersionString() == $this->minimalVersion->getVersionString() ||
        $version->isGreaterThan($this->minimalVersion);
    }

}
