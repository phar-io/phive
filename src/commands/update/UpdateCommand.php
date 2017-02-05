<?php
namespace PharIo\Phive;

use PharIo\Version\Version;

class UpdateCommand implements Cli\Command {

    /**
     * @var UpdateCommandConfig
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
     * @param UpdateCommandConfig $updateCommandConfig
     * @param PharService         $pharService
     * @param PhiveXmlConfig      $phiveXmlConfig
     */
    public function __construct(
        UpdateCommandConfig $updateCommandConfig,
        PharService $pharService,
        PhiveXmlConfig $phiveXmlConfig
    ) {
        $this->config = $updateCommandConfig;
        $this->pharService = $pharService;
        $this->phiveXmlConfig = $phiveXmlConfig;
    }

    public function execute() {
        foreach ($this->config->getRequestedPhars() as $requestedPhar) {
            if (!$requestedPhar->hasAlias()) {
                continue;
            }

            $installedPhar = $this->installOrUpdate($requestedPhar);

            if (null === $installedPhar) {
                continue;
            }

            $this->phiveXmlConfig->addPhar($installedPhar);
        }
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return InstalledPhar|null
     */
    private function install(RequestedPhar $requestedPhar) {
        $targetDirectory = $this->config->getTargetDirectory();
        return $this->pharService->install($requestedPhar, $targetDirectory, false);
    }

    /**
     * @param RequestedPhar $requestedPhar
     * @param Version       $currentVersion
     *
     * @return InstalledPhar|null
     */
    private function update(RequestedPhar $requestedPhar, Version $currentVersion) {
        $location = $this->phiveXmlConfig->getPharLocation($requestedPhar->getAlias()->asString());
        return $this->pharService->update($requestedPhar, $location, $currentVersion);
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return null|InstalledPhar
     */
    private function installOrUpdate(RequestedPhar $requestedPhar) {
        if (!$this->phiveXmlConfig->isPharInstalled($requestedPhar)) {
            return $this->install($requestedPhar);
        }
        $currentVersion = $this->phiveXmlConfig->getPharVersion($requestedPhar);
        return $this->update($requestedPhar, $currentVersion);
    }

}
