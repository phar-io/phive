<?php
namespace PharIo\Phive;

class SpecificMajorVersionConstraint extends AbstractVersionConstraint {

    /**
     * @var int
     */
    private $major = 0;

    /**
     * @param string $originalValue
     * @param int    $major
     */
    public function __construct($originalValue, $major) {
        parent::__construct($originalValue);
        $this->major = $major;
    }

    /**
     * @param Version $version
     *
     * @return bool
     */
    public function complies(Version $version) {
        return $version->getMajor()->getValue() == $this->major;
    }

}



