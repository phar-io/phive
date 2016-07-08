<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class InstallCommand implements Cli\Command {

    /**
     * @var InstallCommandConfig
     */
    private $config;

    /**
     * @var PharService
     */
    private $pharService;

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @param InstallCommandConfig $config
     * @param PharService          $pharService
     * @param PhiveXmlConfig       $phiveXmlConfig
     * @param Environment          $environment
     */
    public function __construct(
        InstallCommandConfig $config,
        PharService $pharService,
        PhiveXmlConfig $phiveXmlConfig,
        Environment $environment
    ) {
        $this->config = $config;
        $this->pharService = $pharService;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->environment = $environment;
    }

    /**
     *
     */
    public function execute() {
        $targetDirectory = $this->getTargetDirectory();

        foreach ($this->getConfig()->getRequestedPhars() as $requestedPhar) {
            $this->installRequestedPhar($requestedPhar, $targetDirectory);
        }
    }

    /**
     * @param RequestedPhar $requestedPhar
     * @param Directory     $targetDirectory
     */
    protected function installRequestedPhar(RequestedPhar $requestedPhar, Directory $targetDirectory) {
        $installedPhar = $this->pharService->install($requestedPhar, $targetDirectory, $this->getConfig()->makeCopy());
        if (null === $installedPhar || $this->getConfig()->doNotAddToPhiveXml()) {
            return;
        }
        $this->phiveXmlConfig->addPhar($requestedPhar, $installedPhar, $targetDirectory);
    }

    /**
     * @return Directory
     */
    protected function getTargetDirectory() {
        if ($this->getConfig()->installGlobally()) {
            return new Directory(dirname($this->environment->getBinaryName()));
        }
        return $this->getConfig()->getTargetDirectory();
    }

    /**
     * @return InstallCommandConfig
     */
    protected function getConfig() {
        return $this->config;
    }

}
