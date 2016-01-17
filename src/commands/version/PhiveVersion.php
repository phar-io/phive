<?php
namespace PharIo\Phive;

class PhiveVersion {

    private $version;

    public function __construct($version = '0.1.0') {
        $this->version = $version;
    }

    public function getVersionString() {
        return sprintf(
            'Phive %s - Copyright (C) 2015-2016 by Arne Blankerts, Sebastian Heuer and Contributors',
            $this->getVersion()
        );
    }

    public function getVersion() {
        return $this->version;
    }

}
