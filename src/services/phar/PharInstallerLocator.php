<?php declare(strict_types = 1);
namespace PharIo\Phive;

class PharInstallerLocator {
    /** @var PharInstallerFactory */
    private $factory;

    public function __construct(PharInstallerFactory $factory) {
        $this->factory = $factory;
    }

    public function getPharInstaller(Environment $environment): PharInstaller {
        if ($environment instanceof WindowsEnvironment) {
            return $this->factory->getWindowsPharInstaller();
        }

        return $this->factory->getUnixoidPharInstaller();
    }
}
