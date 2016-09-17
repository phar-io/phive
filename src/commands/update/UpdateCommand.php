<?php
namespace PharIo\Phive;

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
            $alias = (string)$requestedPhar->getAlias();
            if (!$this->phiveXmlConfig->isPharInstalled($alias)) {
                $installedPhar = $this->install($requestedPhar);
            } else {
                $currentVersion = $this->phiveXmlConfig->getPharVersion($alias);
                $installedPhar = $this->update($requestedPhar, $currentVersion);
            }
            if (null === $installedPhar) {
                continue;
            }
            $this->phiveXmlConfig->addPhar($installedPhar);
        }
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return null|InstalledPhar
     */
    private function install(RequestedPhar $requestedPhar) {
        $targetDirectory = $this->config->getTargetDirectory();
        return $this->pharService->install($requestedPhar, $targetDirectory, false);
    }

    /**
     * @param RequestedPhar $requestedPhar
     * @param Version       $currentVersion
     *
     * @return InstalledPhar
     */
    private function update(RequestedPhar $requestedPhar, Version $currentVersion) {
        $location = $this->phiveXmlConfig->getPharLocation((string)$requestedPhar->getAlias());
        return $this->pharService->update($requestedPhar, $location, $currentVersion);
    }

}
