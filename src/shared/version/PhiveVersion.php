<?php
namespace PharIo\Phive;

abstract class PhiveVersion {

    /**
     * @return string
     */
    public function getVersionString() {
        return sprintf(
            'Phive %s - Copyright (C) 2015-%d by Arne Blankerts, Sebastian Heuer and Contributors',
            $this->getVersion(),
            date('Y')
        );
    }

    /**
     * @return string
     */
    abstract public function getVersion();

}
