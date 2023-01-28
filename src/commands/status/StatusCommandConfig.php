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

use function array_map;
use function array_merge;
use function array_reduce;
use function count;
use function usort;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\CommandOptionsException;
use PharIo\Version\AnyVersionConstraint;

class StatusCommandConfig {
    /** @var Cli\Options */
    private $cliOptions;

    /** @var PhiveXmlConfig */
    private $phiveXmlConfig;

    /** @var PharRegistry */
    private $pharRegistry;

    public function __construct(Cli\Options $options, PhiveXmlConfig $phiveXmlConfig, PharRegistry $pharRegistry) {
        $this->cliOptions     = $options;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->pharRegistry   = $pharRegistry;
    }

    /**
     * @throws CommandOptionsException
     * @throws ConfigException
     */
    public function getPhars(): array {
        if (!$this->allInstalled()) {
            return $this->phiveXmlConfig->getPhars();
        }

        $usedPhar = array_map(static function (UsedPhar $phar) {
            if (count($phar->getUsages()) === 0) {
                return [new ConfiguredPhar(
                    $phar->getName(),
                    new AnyVersionConstraint(),
                    $phar->getVersion()
                )];
            }

            return array_map(static function (string $path) use ($phar) {
                return new ConfiguredPhar(
                    $phar->getName(),
                    new AnyVersionConstraint(),
                    $phar->getVersion(),
                    new Filename($path)
                );
            }, $phar->getUsages());
        }, $this->pharRegistry->getAllPhars());

        $usedPhar = array_reduce($usedPhar, static function (array $accumulator, array $items) {
            return array_merge($accumulator, $items);
        }, []);

        usort($usedPhar, static function (ConfiguredPhar $pharA, ConfiguredPhar $pharB) {
            return [$pharA->getName(), $pharA->getInstalledVersion()]
                <=> [$pharB->getName(), $pharB->getInstalledVersion()];
        });

        return $usedPhar;
    }

    public function allInstalled(): bool {
        return $this->cliOptions->hasOption('all');
    }

    public function globalInstalled(): bool {
        return $this->cliOptions->hasOption('global');
    }
}
