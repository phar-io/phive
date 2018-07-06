<?php
namespace PharIo\Phive;

use PharIo\Version\Version;

interface Release {

    /**
     * @return boolean
     */
    public function isSupported();

    /**
     * @return Version
     */
    public function getVersion();

    /**
     * @return string
     */
    public function getName();

}
