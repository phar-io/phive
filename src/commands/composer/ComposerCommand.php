<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class ComposerCommand extends InstallCommand {

    /**
     * @var ComposerService
     */
    private $composerService;

    /**
     * @var Cli\Input
     */
    private $input;

    /**
     * @param ComposerCommandConfig $config
     * @param ComposerService       $composerService
     * @param PharService           $pharService
     * @param PhiveXmlConfig        $phiveXmlConfig
     * @param Environment           $environment
     * @param Cli\Input             $input
     */
    public function __construct(ComposerCommandConfig $config, ComposerService $composerService, PharService $pharService, PhiveXmlConfig $phiveXmlConfig, Environment $environment, Cli\Input $input) {
        parent::__construct($config, $pharService, $phiveXmlConfig, $environment);
        $this->composerService = $composerService;
        $this->input = $input;
    }

    public function execute() {
        $targetDirectory = $this->getTargetDirectory();

        foreach ($this->composerService->findCandidates($this->getConfig()->getComposerFilename()) as $candidate) {
            if (!$this->input->confirm(sprintf('Install %s ?', $candidate->getAlias()))) {
                continue;
            }
            $this->installRequestedPhar($candidate, $targetDirectory);
        }
    }

    /**
     * @return ComposerCommandConfig
     */
    protected function getConfig() {
        return parent::getConfig();
    }

}
