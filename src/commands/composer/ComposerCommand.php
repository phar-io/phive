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
     * @param ComposerCommandConfig        $config
     * @param ComposerService              $composerService
     * @param InstallService               $installService
     * @param Cli\Input                    $input
     * @param RequestedPharResolverService $pharResolver
     *
     * @internal param PhiveXmlConfig $phiveXmlConfig
     */
    public function __construct(
        ComposerCommandConfig $config,
        ComposerService $composerService,
        InstallService $installService,
        Cli\Input $input,
        RequestedPharResolverService $pharResolver,
        ReleaseSelector $selector
    ) {
        parent::__construct($config, $installService, $pharResolver, $selector);
        $this->composerService = $composerService;
        $this->input = $input;
    }

    public function execute() {
        $targetDirectory = $this->getConfig()->getTargetDirectory();

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
