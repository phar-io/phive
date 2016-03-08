<?php
namespace PharIo\Phive;

class UpdateCommandConfig
{

    /**
     * @var Cli\Options
     */
    private $cliOptions;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;

    /**
     * @param Cli\Options $cliOptions
     * @param Config $config
     * @param PhiveXmlConfig $phiveXmlConfig
     */
    public function __construct(
        Cli\Options $cliOptions,
        Config $config,
        PhiveXmlConfig $phiveXmlConfig
    ) {
        $this->cliOptions = $cliOptions;
        $this->config = $config;
        $this->phiveXmlConfig = $phiveXmlConfig;
    }

    /**
     * @return RequestedPhar[]
     */
    public function getRequestedPhars() {
        $filter = $this->getPharsFromCliArguments();
        return $this->getPharAliasesFromPhiveXmlConfig($filter);
    }

    /**
     * @return Directory
     */
    public function getWorkingDirectory() {
        return $this->config->getWorkingDirectory();
    }

    /**
     * @param array $filter
     *
     * @return RequestedPhar[]
     */
    private function getPharAliasesFromPhiveXmlConfig(array $filter) {
        $phars = [];
        foreach ($this->phiveXmlConfig->getPhars() as $phar) {
            if (!empty($filter) && !in_array((string)($phar->getAlias()), $filter)) {
                continue;
            }
            $phars[] = $phar;
        }
        return $phars;
    }

    /**
     * @return string[]
     * @throws CLI\CommandOptionsException
     */
    private function getPharsFromCliArguments() {
        $phars = [];
        $argCount = $this->cliOptions->getArgumentCount();
        for ($i = 0; $i < $argCount; $i++) {
            $phars[] = $this->cliOptions->getArgument($i);
        }
        return $phars;
    }
}