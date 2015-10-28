<?php
namespace PharIo\Phive;

class AnyVersionConstraint implements VersionConstraintInterface
{
    /**
     * @param Version $version
     *
     * @return bool
     */
    public function complies(Version $version)
    {
        return true;
    }

}