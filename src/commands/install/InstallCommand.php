<?php
namespace PharIo\Phive;

use TheSeer\CLI\Command;

class InstallCommand implements Command {

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
     * @param InstallCommandConfig $config
     * @param PharService          $pharService
     * @param PhiveXmlConfig       $phiveXmlConfig
     */
    public function __construct(
        InstallCommandConfig $config,
        PharService $pharService,
        PhiveXmlConfig $phiveXmlConfig
    ) {
        $this->config = $config;
        $this->pharService = $pharService;
        $this->phiveXmlConfig = $phiveXmlConfig;
    }

    /**
     *
     */
    public function execute() {
        foreach ($this->config->getRequestedPhars() as $requestedPhar) {
            $this->pharService->install($requestedPhar, $this->config->getWorkingDirectory());
            if ($this->config->saveToPhiveXml()) {
                $this->phiveXmlConfig->addPhar($requestedPhar);
            }
        }
    }

}


