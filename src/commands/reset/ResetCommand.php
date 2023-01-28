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

use function in_array;

class ResetCommand implements Cli\Command {
    /** @var ResetCommandConfig */
    private $config;

    /** @var PharRegistry */
    private $pharRegistry;

    /** @var Environment */
    private $environment;

    /** @var PharInstaller */
    private $pharInstaller;

    public function __construct(
        ResetCommandConfig $config,
        PharRegistry $pharRegistry,
        Environment $environment,
        PharInstaller $pharInstaller
    ) {
        $this->config        = $config;
        $this->pharRegistry  = $pharRegistry;
        $this->environment   = $environment;
        $this->pharInstaller = $pharInstaller;
    }

    public function execute(): void {
        $aliasFilter = [];

        if ($this->config->hasAliases()) {
            $aliasFilter = $this->config->getAliases();
        }

        foreach ($this->pharRegistry->getUsedPharsByDestination($this->environment->getWorkingDirectory()) as $phar) {
            if (!empty($aliasFilter) && !in_array($phar->getName(), $aliasFilter, true)) {
                continue;
            }
            $this->pharInstaller->install($phar->getFile(), $this->environment->getWorkingDirectory()->file($phar->getName()), false);
        }
    }
}
