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
use PharIo\FileSystem\Directory;

class UpdateCommandConfig {
    /** @var Cli\Options */
    private $cliOptions;

    /** @var PhiveXmlConfig */
    private $phiveXmlConfig;

    /** @var TargetDirectoryLocator */
    private $targetDirectoryLocator;

    public function __construct(
        Cli\Options $cliOptions,
        PhiveXmlConfig $phiveXmlConfig,
        TargetDirectoryLocator $targetDirectoryLocator
    ) {
        $this->cliOptions             = $cliOptions;
        $this->phiveXmlConfig         = $phiveXmlConfig;
        $this->targetDirectoryLocator = $targetDirectoryLocator;
    }

    /**
     * @return RequestedPhar[]
     */
    public function getRequestedPhars(): array {
        $filter = $this->getPharsFromCliArguments();

        return $this->getPharAliasesFromPhiveXmlConfig($filter);
    }

    public function getTargetDirectory(): Directory {
        return $this->targetDirectoryLocator->getTargetDirectory();
    }

    public function preferOffline(): bool {
        return $this->cliOptions->hasOption('prefer-offline');
    }

    public function forceAcceptUnsignedPhars(): bool {
        return $this->cliOptions->hasOption('force-accept-unsigned');
    }

    /**
     * @return RequestedPhar[]
     */
    private function getPharAliasesFromPhiveXmlConfig(array $filter): array {
        $phars = [];

        foreach ($this->phiveXmlConfig->getPhars() as $configuredPhar) {
            if (!empty($filter) && !in_array($configuredPhar->getName(), $filter, true)) {
                continue;
            }

            if (Url::isUrl($configuredPhar->getName())) {
                $identifier = new PharUrl($configuredPhar->getName());
            } elseif ($configuredPhar->hasUrl()) {
                $identifier = new PharUrl($configuredPhar->getUrl()->asString());
            } else {
                $identifier = new PharAlias($configuredPhar->getName());
            }

            $location = $configuredPhar->hasLocation() ? $configuredPhar->getLocation() : null;

            $phars[] = new RequestedPhar(
                $identifier,
                $configuredPhar->getVersionConstraint(),
                $configuredPhar->getVersionConstraint(),
                $location,
                $configuredPhar->isCopy()
            );
        }

        return $phars;
    }

    /**
     * @throws Cli\CommandOptionsException
     *
     * @return string[]
     */
    private function getPharsFromCliArguments(): array {
        $phars    = [];
        $argCount = $this->cliOptions->getArgumentCount();

        for ($i = 0; $i < $argCount; $i++) {
            $phars[] = $this->cliOptions->getArgument($i);
        }

        return $phars;
    }
}
