<?php
namespace PharIo\Phive;

interface PharActivator {

    /**
     * @param Filename $pharLocation
     * @param Filename $linkDestination
     *
     * @return Filename
     */
    public function activate(Filename $pharLocation, Filename $linkDestination);
}
