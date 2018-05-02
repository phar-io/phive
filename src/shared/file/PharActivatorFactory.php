<?php
namespace PharIo\Phive;

class PharActivatorFactory {

    /**
     * @return BatPharActivator
     */
    public function getBatPharActivator() {
        return new BatPharActivator(file_get_contents(__DIR__ . '/../../../conf/pharBat.template'));
    }

    /**
     * @return SymlinkPharActivator
     */
    public function getSymlinkPharActivator() {
        return new SymlinkPharActivator();
    }


    /**
     * @return WindowsPharActivator
     */
    public function getWindowsPharActivator() {
        return new WindowsPharActivator(file_get_contents(__DIR__ . '/../../../conf/pharBat.template'));
    }

}
