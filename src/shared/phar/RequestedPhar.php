<?php
namespace PharIo\Phive;

interface RequestedPhar {

    /**
     * @return bool
     */
    public function isAlias();

    /**
     * @return PharAlias
     */
    public function getAlias();

    /**
     * @return PharUrl
     */
    public function getPharUrl();

    /**
     * @return VersionConstraint
     */
    public function getVersionConstraint();

}
