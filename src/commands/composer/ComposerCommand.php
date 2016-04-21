<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class ComposerCommand implements Cli\Command {

    /**
     * @var ComposerCommandConfig
     */
    private $config;

    /**
     * @var ComposerService
     */
    private $composerService;

    /**
     * @var PharService
     */
    private $pharService;

    /**
     * @var Cli\Input
     */
    private $input;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;

    /**
     * @param ComposerCommandConfig $config
     * @param ComposerService       $composerService
     * @param PharService           $pharService
     * @param PhiveXmlConfig        $phiveXmlConfig
     * @param Environment           $environment
     * @param Cli\Input             $input
     */
    public function __construct(ComposerCommandConfig $config, ComposerService $composerService, PharService $pharService, PhiveXmlConfig $phiveXmlConfig, Environment $environment, Cli\Input $input) {
        $this->config = $config;
        $this->composerService = $composerService;
        $this->pharService = $pharService;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->environment = $environment;
        $this->input = $input;
    }

    public function execute() {
        if ($this->config->installGlobally()) {
            $targetDirectory = dirname($this->environment->getBinaryName());
        } else {
            $targetDirectory = $this->config->getWorkingDirectory();
        }

        foreach ($this->composerService->findCandidates($this->config->getComposerFilename()) as $candidate) {
            if (!$this->input->confirm(
                sprintf('Install %s ?', $candidate->getAlias()))
            ) {
                continue;
            }

            $this->pharService->install($candidate, $targetDirectory, $this->config->makeCopy());
            if ($this->config->doNotAddToPhiveXml()) {
                continue;
            }
            $this->phiveXmlConfig->addPhar($candidate);
        }
    }

}
