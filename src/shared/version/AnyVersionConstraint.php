<?php
namespace PharIo\Phive;

class AnyVersionConstraint implements VersionConstraintInterface
{
    /**
     * @param Version $version
     *
     * @return bool
     */
    public function matches(Version $version)
    {
        return true;
    }

}