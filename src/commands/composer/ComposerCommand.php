<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ComposerCommand extends InstallCommand {

    /** @var ComposerService */
    private $composerService;

    /** @var Cli\Input */
    private $input;

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
        $this->input           = $input;
    }

    public function execute(): void {
        $targetDirectory = $this->getConfig()->getTargetDirectory();

        foreach ($this->composerService->findCandidates($this->getConfig()->getComposerFilename()) as $candidate) {
            if (!$this->input->confirm(\sprintf('Install %s ?', $candidate->asString()))) {
                continue;
            }
            $this->installRequestedPhar($candidate, $targetDirectory);
        }
    }

    protected function getConfig() {
        return parent::getConfig();
    }
}
