<?php
namespace PharIo\Phive;

class ResetCommand implements Cli\Command {

    /**
     * @var ResetCommandConfig
     */
    private $config;

    /**
     * @var PhiveInstallDB
     */
    private $installDB;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var PharInstaller
     */
    private $pharInstaller;

    /**
     * @param ResetCommandConfig $config
     * @param PhiveInstallDB $installDB
     * @param Environment $environment
     * @param PharInstaller $pharInstaller
     */
    public function __construct(
        ResetCommandConfig $config,
        PhiveInstallDB $installDB,
        Environment $environment,
        PharInstaller $pharInstaller
    ) {
        $this->config = $config;
        $this->installDB = $installDB;
        $this->environment = $environment;
        $this->pharInstaller = $pharInstaller;
    }

    public function execute() {
        $aliasFilter = [];

        if ($this->config->hasAliases()) {
            $aliasFilter = $this->config->getAliases();
        }
        

        foreach ($this->installDB->getUsedPharsByDestination($this->environment->getWorkingDirectory()) as $phar) {
            if (!empty($aliasFilter) && !in_array($phar->getName(), $aliasFilter)) {
                continue;
            }
            $this->pharInstaller->install($phar->getFile(), $this->environment->getWorkingDirectory()->file($phar->getName()), false);
        }
    }

}