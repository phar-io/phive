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
        if ($this->config->installGlobally()) {
            $targetDirectory = new Directory(dirname($this->environment->getBinaryName()));
        } else {
            $targetDirectory = $this->config->getTargetDirectory();
        }

        if (!$this->phiveXmlConfig->hasTargetDirectory() && !$this->config->doNotAddToPhiveXml()) {
            $this->phiveXmlConfig->setTargetDirectory($targetDirectory);
        }

        foreach ($this->config->getRequestedPhars() as $requestedPhar) {
            $this->pharService->install($requestedPhar, (string)$targetDirectory, $this->config->makeCopy());
            if ($this->config->doNotAddToPhiveXml()) {
                continue;
            }
            $this->phiveXmlConfig->addPhar($requestedPhar);
        }
    }

}
