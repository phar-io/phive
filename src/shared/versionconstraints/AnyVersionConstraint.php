<?php
namespace PharIo\Phive;

class AnyVersionConstraint implements VersionConstraint {

    /**
     * @param Version $version
     *
     * @return bool
     */
    public function complies(Version $version) {
        return true;
    }

    /**
     * @return string
     */
    public function asString() {
        return '*';
    }

}



