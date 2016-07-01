<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class RemoveCommand implements Cli\Command {

    /**
     * @var RemoveCommandConfig
     */
    private $config;

    /**
     * @var PharRegistry
     */
    private $repository;

    /**
     * @var PharService
     */
    private $pharService;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;

    /**
     * @param RemoveCommandConfig $config
     * @param PharRegistry $repository
     * @param PharService $pharService
     * @param Cli\Output $output
     * @param PhiveXmlConfig $phiveXmlConfig
     */
    public function __construct(
        RemoveCommandConfig $config,
        PharRegistry $repository,
        PharService $pharService,
        Cli\Output $output,
        PhiveXmlConfig $phiveXmlConfig
    ) {
        $this->config = $config;
        $this->repository = $repository;
        $this->pharService = $pharService;
        $this->output = $output;
        $this->phiveXmlConfig = $phiveXmlConfig;
    }

    public function execute() {
        $destination = $this->config->getTargetDirectory() . '/' . $this->config->getPharName();
        $phar = $this->repository->getByUsage($destination);
        $this->output->writeInfo(
            sprintf('Removing Phar %s %s', $phar->getName(), $phar->getVersion()->getVersionString())
        );
        $this->phiveXmlConfig->removePhar($phar->getName());
        $this->repository->removeUsage($phar, $destination);
        unlink($destination);

        if (!$this->repository->hasUsages($phar)) {
            $this->output->writeInfo(
                sprintf(
                    'Phar %s %s has no more known usages. You can run \'phive purge\' to remove unused Phars.',
                    $phar->getName(),
                    $phar->getVersion()->getVersionString()
                )
            );
        }
    }

}
