<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function sprintf;

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
            if (!$this->input->confirm(sprintf('Install %s ?', $candidate->asString()))) {
                continue;
            }
            $this->installRequestedPhar($candidate, $targetDirectory);
        }
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     *
     * @psalm-return ComposerCommandConfig
     */
    protected function getConfig(): InstallCommandConfig {
        $config = parent::getConfig();
        assert($config instanceof ComposerCommandConfig);

        return $config;
    }
}
