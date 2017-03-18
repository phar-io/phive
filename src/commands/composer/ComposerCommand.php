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
     * @param ComposerService $composerService
     * @param InstallService $installService
     * @param PhiveXmlConfig $phiveXmlConfig
     * @param Environment $environment
     * @param Cli\Input $input
     * @param RequestedPharResolverService $pharResolver
     */
    public function __construct(
        ComposerCommandConfig $config,
        ComposerService $composerService,
        InstallService $installService,
        PhiveXmlConfig $phiveXmlConfig,
        Environment $environment,
        Cli\Input $input,
        RequestedPharResolverService $pharResolver
    ) {
        parent::__construct($config, $installService, $environment, $pharResolver);
        $this->composerService = $composerService;
        $this->input = $input;
    }

    public function execute() {
        $targetDirectory = $this->getTargetDirectory();

        foreach ($this->composerService->findCandidates($this->getConfig()->getComposerFilename()) as $candidate) {
            if (!$this->input->confirm(sprintf('Install %s ?', $candidate->asString()))) {
                continue;
            }
            $this->installRequestedPhar($candidate, $targetDirectory);
        }
    }

    /**
     * @return InstallCommandConfig|ComposerCommandConfig
     */
    protected function getConfig() {
        return parent::getConfig();
    }

}
