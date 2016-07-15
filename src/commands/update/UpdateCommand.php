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
        $targetDirectory = $this->config->getTargetDirectory();

        foreach ($this->config->getRequestedPhars() as $requestedPhar) {

            $pharName = (string)$requestedPhar->getAlias();

            if ($this->phiveXmlConfig->hasPharLocation($pharName)) {
                $targetDirectory = new Directory(dirname($this->phiveXmlConfig->getPharLocation($pharName)));
            }

            $installedPhar = $this->pharService->update($requestedPhar, $targetDirectory);
            if (null === $installedPhar) {
                continue;
            }
            $this->phiveXmlConfig->addPhar($installedPhar);
        }
    }

}
