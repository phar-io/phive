<?php
namespace PharIo\Phive;

class PharInstallerFactory {

    /**
     * @var Factory
     */
    private $factory;

    /**
    * @param Factory $factory
     */
    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    public function getWindowsPharInstaller() {
        return new WindowsPharInstaller($this->factory->getOutput(), file_get_contents(__DIR__ . '/../../../conf/pharBat.template'));
    }

    public function getUnixoidPharInstaller() {
        return new UnixoidPharInstaller($this->factory->getOutput());
    }

}
