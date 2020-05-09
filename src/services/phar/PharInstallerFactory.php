<?php declare(strict_types = 1);
namespace PharIo\Phive;

class PharInstallerFactory {
    /** @var Factory */
    private $factory;

    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    public function getWindowsPharInstaller(): WindowsPharInstaller {
        return new WindowsPharInstaller($this->factory->getOutput(), \file_get_contents(__DIR__ . '/../../../conf/pharBat.template'));
    }

    public function getUnixoidPharInstaller(): UnixoidPharInstaller {
        return new UnixoidPharInstaller($this->factory->getOutput());
    }
}
