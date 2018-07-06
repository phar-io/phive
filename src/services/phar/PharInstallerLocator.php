<?php
namespace PharIo\Phive;

class PharInstallerLocator {

    /**
     * @var PharInstallerFactory
     */
    private $factory;

    /**
     * @param PharInstallerFactory $factory
     */
    public function __construct(PharInstallerFactory $factory) {
        $this->factory = $factory;
    }

    /**
     * @param Environment $environment
     *
     * @return PharInstaller
     */
    public function getPharInstaller(Environment $environment) {
        if ($environment instanceof WindowsEnvironment) {
            return $this->factory->getWindowsPharInstaller();
        }

        return $this->factory->getUnixoidPharInstaller();
    }

}
