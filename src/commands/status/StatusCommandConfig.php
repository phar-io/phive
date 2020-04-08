<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
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
     * @throws \PharIo\Phive\ConfigException
     * @throws \PharIo\Phive\Cli\CommandOptionsException
     */
    public function getPhars(): array {
        if (!$this->allInstalled()) {
            return $this->phiveXmlConfig->getPhars();
        }

        $usedPhar = array_map(function (UsedPhar $phar) {
            if (count($phar->getUsages()) === 0) {
                return [new ConfiguredPhar(
                    $phar->getName(),
                    new AnyVersionConstraint(),
                    $phar->getVersion()
                )];
            }

            return array_map(function (string $path) use ($phar) {
                return new ConfiguredPhar(
                    $phar->getName(),
                    new AnyVersionConstraint(),
                    $phar->getVersion(),
                    new Filename($path)
                );
            }, $phar->getUsages());
        }, $this->pharRegistry->getAllPhars());

        $usedPhar = array_reduce($usedPhar, function ($accumulator, array $items) {
            return array_merge($accumulator, $items);
        }, []);

        usort($usedPhar, function (ConfiguredPhar $pharA, ConfiguredPhar $pharB) {
            return [$pharA->getName(), $pharA->getInstalledVersion()]
                <=> [$pharB->getName(), $pharB->getInstalledVersion()];
        });

        return $usedPhar;
    }

    public function allInstalled(): bool {
        return $this->cliOptions->hasOption('all');
    }
}
